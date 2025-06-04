<?php

declare(strict_types=1);

/**
 * This file is part of DOCtor-RST.
 *
 * (c) Oskar Stark <oskarstark@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\Rule;

use App\Rule\OrderedUseStatements;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class OrderedUseStatementsTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new OrderedUseStatements())->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    public static function checkProvider(): iterable
    {
        $codeBlocks = self::phpCodeBlocks();

        // VALID
        foreach ($codeBlocks as $codeBlock) {
            // WITH blank line after directive
            yield [
                NullViolation::create(),
                new RstSample([
                    $codeBlock,
                    '',
                    '    use Symfony\A;',
                    '    use Symfony\B;',
                ]),
            ];

            // WITHOUT blank line after directive
            yield [
                NullViolation::create(),
                new RstSample([
                    $codeBlock,
                    '    use Symfony\A;',
                    '    use Symfony\B;',
                ]),
            ];
        }

        // INVALID
        foreach ($codeBlocks as $codeBlock) {
            // WITH blank line after directive
            yield [
                Violation::from(
                    'Please reorder the use statements alphabetically',
                    'filename',
                    1,
                    $codeBlock,
                ),
                new RstSample([
                    $codeBlock,
                    '',
                    '    use Symfony\B;',
                    '    use Symfony\A;',
                ]),
            ];

            // WITHOUT blank line after directive
            yield [
                Violation::from(
                    'Please reorder the use statements alphabetically',
                    'filename',
                    1,
                    $codeBlock,
                ),
                new RstSample([
                    $codeBlock,
                    '    use Symfony\B;',
                    '    use Symfony\A;',
                ]),
            ];
        }

        $valid_with_trait = <<<'RST'
In addition, the Console component provides a PHP trait called ``LockableTrait``
that adds two convenient methods to lock and release commands::

    // ...
    use Symfony\Component\Console\Command\Command;
    use Symfony\Component\Console\Command\LockableTrait;
    use Symfony\Component\Console\Input\InputInterface;
    use Symfony\Component\Console\Output\OutputInterface;

    class UpdateContentsCommand extends Command
    {
        use LockableTrait;
RST;

        $valid_with_two_code_examples_in_one_block = <<<'RST'
    .. code-block:: php

        // src/AppBundle/Entity/Address.php
        namespace AppBundle\Entity;

        use Symfony\Component\Validator\Constraints as Assert;
        use Symfony\Component\Validator\Mapping\ClassMetadata;

        class Address
        {
            protected $street;
            protected $zipCode;

            public static function loadValidatorMetadata(ClassMetadata $metadata)
            {
                $metadata->addPropertyConstraint('street', new Assert\NotBlank());
                $metadata->addPropertyConstraint('zipCode', new Assert\NotBlank());
                $metadata->addPropertyConstraint('zipCode', new Assert\Length(["max" => 5]));
            }
        }

        // src/AppBundle/Entity/Author.php
        namespace AppBundle\Entity;

        use Symfony\Component\Validator\Constraints as Assert;
        use Symfony\Component\Validator\Mapping\ClassMetadata;

        class Author
        {
            protected $firstName;
            protected $lastName;
            protected $address;

            public static function loadValidatorMetadata(ClassMetadata $metadata)
            {
                $metadata->addPropertyConstraint('firstName', new Assert\NotBlank());
                $metadata->addPropertyConstraint('firstName', new Assert\Length(["min" => 4]));
                $metadata->addPropertyConstraint('lastName', new Assert\NotBlank());
            }
        }
RST;

        $valid_with_nsort = <<<'RST'
.. code-block:: php

    use AppBundle\EventListener\SearchIndexer;
    use AppBundle\EventListener\SearchIndexer2;
    use AppBundle\EventListener\SearchIndexerSubscriber;
RST;

        $valid_with_use_statement_in_comment = <<<'RST'
a ``postPersist()`` method, which will be called when the event is dispatched::

    // src/AppBundle/EventListener/SearchIndexer.php
    namespace AppBundle\EventListener;

    use AppBundle\Entity\Product;
    // for Doctrine < 2.4: use Doctrine\ORM\Event\LifecycleEventArgs;
    use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

    class SearchIndexer
RST;

        $valid_without_class_but_variable_in_between = <<<'RST'
Instead of loading each file manually, you'll only have to register the
generated class map with, for example, the
:class:`Symfony\\Component\\ClassLoader\\MapClassLoader`::

    use Symfony\Component\ClassLoader\MapClassLoader;

    $mapping = include __DIR__.'/class_map.php';
    $loader = new MapClassLoader($mapping);
    $loader->register();

    // you can now use the classes:
    use Acme\Foo;

    $foo = new Foo();

    // ...
RST;

        $valid_with_uppercase = <<<'RST'
for that::

    // src/AppBundle/Form/Type/LocationType.php
    namespace AppBundle\Form\Type;

    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\Extension\Core\Type\TextareaType;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;

    class LocationType extends AbstractType
RST;

        $valid2 = <<<'RST'
the user::

    use Symfony\Component\HttpFoundation\RequestMatcher;
    use Symfony\Component\Security\Http\Firewall\ExceptionListener;
    use Symfony\Component\Security\Http\FirewallMap;

    $firewallMap = new FirewallMap();
RST;

        $valid_in_trait_definition = <<<'RST'
This  allows you to create helper traits like RouterAware, LoggerAware, etc...
and compose your services with them::

    // src/Service/LoggerAware.php
    namespace App\Service;

    use Psr\Log\LoggerInterface;

    trait LoggerAware
    {
        private function logger(): LoggerInterface
        {
            return $this->container->get(__CLASS__.'::'.__FUNCTION__);
        }
    }

    // src/Service/RouterAware.php
    namespace App\Service;

    use Symfony\Component\Routing\RouterInterface;

    trait RouterAware
    {
        private function router(): RouterInterface
        {
            return $this->container->get(__CLASS__.'::'.__FUNCTION__);
        }
    }

    // src/Service/MyService.php
    namespace App\Service;

    use Symfony\Component\DependencyInjection\ServiceSubscriberInterface;
    use Symfony\Component\DependencyInjection\ServiceSubscriberTrait;

    class MyService implements ServiceSubscriberInterface
    {
        use ServiceSubscriberTrait, LoggerAware, RouterAware;

        public function doSomething()
        {
            // $this->router() ...
            // $this->logger() ...
        }
    }
RST;

        $valid_with_function = <<<'RST'
for that::

    use Symfony\Component\Form\AbstractType;
    use function Symfony\foo;
RST;

        $valid3 = <<<'RST'
and storage::

    // src/Security/NormalizedUserBadge.php
    namespace App\Security;

    use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
    use function Symfony\Component\String\u;

    final class NormalizedUserBadge extends UserBadge
    {
        public function __construct(string $identifier)
        {
            $callback = static fn (string $identifier): string => u($identifier)->normalize(UnicodeString::NFKC)->ascii()->lower()->toString();

            parent::__construct($identifier, null, $callback);
        }
    }

::

    // src/Security/PasswordAuthenticator.php
    namespace App\Security;

    final class PasswordAuthenticator extends AbstractLoginFormAuthenticator
    {
        // simplified for brevity
        public function authenticate(Request $request): Passport
        {
            $username = (string) $request->request->get('username', '');
            $password = (string) $request->request->get('password', '');

            $request->getSession()
                ->set(SecurityRequestAttributes::LAST_USERNAME, $username);

            return new Passport(
                new NormalizedUserBadge($username),
                new PasswordCredentials($password),
                [
                    // all other useful badges
                ]
            );
        }
    }

User Credential
~~~~~~~~~~~~~~~
RST;

        yield 'valid with trait' => [
            NullViolation::create(),
            new RstSample($valid_with_trait, 1),
        ];
        yield 'valid with 2 code examples in one block' => [
            NullViolation::create(),
            new RstSample($valid_with_two_code_examples_in_one_block),
        ];
        yield 'valid with nsort' => [
            NullViolation::create(),
            new RstSample($valid_with_nsort),
        ];
        yield 'valid with use statement in comment' => [
            NullViolation::create(),
            new RstSample($valid_with_use_statement_in_comment),
        ];
        yield 'valid without class but variable in between' => [
            NullViolation::create(),
            new RstSample($valid_without_class_but_variable_in_between, 2),
        ];
        yield 'valid with uppercase' => [
            NullViolation::create(),
            new RstSample($valid_with_uppercase),
        ];
        yield 'valid 2' => [
            NullViolation::create(),
            new RstSample($valid2),
        ];
        yield 'valid in trait definition' => [
            NullViolation::create(),
            new RstSample($valid_in_trait_definition, 1),
        ];
        yield 'valid with function' => [
            NullViolation::create(),
            new RstSample($valid_with_function, 1),
        ];
        yield 'valid 3' => [
            NullViolation::create(),
            new RstSample($valid3),
        ];
    }
}
