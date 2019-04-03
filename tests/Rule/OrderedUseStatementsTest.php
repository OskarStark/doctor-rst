<?php

declare(strict_types=1);

/*
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
use PHPUnit\Framework\TestCase;

class OrderedUseStatementsTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check($expected, RstSample $sample)
    {
        $this->assertSame(
            $expected,
            (new OrderedUseStatements())->check($sample->getContent(), $sample->getLineNumber())
        );
    }

    public function checkProvider(): \Generator
    {
        $codeBlocks = [
            '.. code-block:: php',
            '.. code-block:: php-annotations',
            'A php code block follows::',
        ];

        // VALID
        foreach ($codeBlocks as $codeBlock) {
            // WITH blank line after directive
            yield [
                null,
                new RstSample([
                    $codeBlock,
                    '',
                    '    use Symfony\A;',
                    '    use Symfony\B;',
                ]),
            ];

            // WITHOUT blank line after directive
            yield [
                null,
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
                'Please reorder the use statements alphabetical',
                new RstSample([
                    $codeBlock,
                    '',
                    '    use Symfony\B;',
                    '    use Symfony\A;',
                ]),
            ];

            // WITHOUT blank line after directive
            yield [
                'Please reorder the use statements alphabetical',
                new RstSample([
                    $codeBlock,
                    '    use Symfony\B;',
                    '    use Symfony\A;',
                ]),
            ];
        }

        $valid_with_trait = <<<'CONTENT'
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
CONTENT;

        $valid_with_two_code_examples_in_one_block = <<<'CONTENT'
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
CONTENT;

        $valid_with_nsort = <<<'CONTENT'
.. code-block:: php

    use AppBundle\EventListener\SearchIndexer;
    use AppBundle\EventListener\SearchIndexer2;
    use AppBundle\EventListener\SearchIndexerSubscriber;
CONTENT;

        $valid_with_use_statement_in_comment = <<<'CONTENT'
a ``postPersist()`` method, which will be called when the event is dispatched::

    // src/AppBundle/EventListener/SearchIndexer.php
    namespace AppBundle\EventListener;

    use AppBundle\Entity\Product;
    // for Doctrine < 2.4: use Doctrine\ORM\Event\LifecycleEventArgs;
    use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

    class SearchIndexer
CONTENT;

        $valid_without_class_but_variable_in_between = <<<'CONTENT'
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
CONTENT;

        $valid_with_uppercase = <<<'CONTENT'
for that::

    // src/AppBundle/Form/Type/LocationType.php
    namespace AppBundle\Form\Type;

    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\Extension\Core\Type\TextareaType;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;

    class LocationType extends AbstractType
CONTENT;

        $valid2 = <<<'CONTENT'
the user::

    use Symfony\Component\HttpFoundation\RequestMatcher;
    use Symfony\Component\Security\Http\Firewall\ExceptionListener;
    use Symfony\Component\Security\Http\FirewallMap;

    $firewallMap = new FirewallMap();
CONTENT;

        yield 'valid with trait' => [
            null,
            new RstSample($valid_with_trait, 1),
        ];
        yield 'valid with 2 code examples in one block' => [
            null,
            new RstSample($valid_with_two_code_examples_in_one_block),
        ];
        yield 'valid with nsort' => [
            null,
            new RstSample($valid_with_nsort),
        ];
        yield 'valid with use statement in comment' => [
            null,
            new RstSample($valid_with_use_statement_in_comment),
        ];
        yield 'valid without class but variable in between' => [
            null,
            new RstSample($valid_without_class_but_variable_in_between, 2),
        ];
        yield 'valid with uppercase' => [
            null,
            new RstSample($valid_with_uppercase),
        ];
        yield 'valid 2' => [
            null,
            new RstSample($valid2),
        ];
    }
}
