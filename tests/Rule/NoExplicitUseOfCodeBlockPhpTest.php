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

use App\Rule\NoExplicitUseOfCodeBlockPhp;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class NoExplicitUseOfCodeBlockPhpTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    #[DataProvider('realSymfonyFileProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new NoExplicitUseOfCodeBlockPhp())->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    /**
     * @return \Generator<array{0: ViolationInterface, 1: RstSample}>
     */
    public static function checkProvider(): iterable
    {
        yield [
            NullViolation::create(),
            new RstSample('Check the following controller syntax::'),
        ];

        yield [
            Violation::from(
                'Please do not use ".. code-block:: php", use "::" instead.',
                'filename',
                1,
                '.. code-block:: php',
            ),
            new RstSample('.. code-block:: php'),
        ];

        yield [
            NullViolation::create(),
            new RstSample('.. code-block:: html+php'),
        ];
        yield [
            Violation::from(
                'Please do not use ".. code-block:: php", use "::" instead.',
                'filename',
                1,
                '.. code-block:: php',
            ),
            new RstSample('    .. code-block:: php'),
        ];

        yield [
            Violation::from(
                'Please do not use ".. code-block:: php", use "::" instead.',
                'filename',
                3,
                '.. code-block:: php',
            ),
            new RstSample([
                'Welcome to our tutorial!',
                '',
                '     .. code-block:: php',
                '',
                '         namespace App\Entity;',
            ], 2),
        ];

        yield [
            NullViolation::create(),
            new RstSample([
                '.. configuration-block::',
                '',
                '    .. code-block:: php',
                '',
                '        namespace App\Entity;',
            ], 2),
        ];
        yield [
            NullViolation::create(),
            new RstSample([
                '    .. configuration-block::',
                '',
                '        .. code-block:: xml',
                '',
                '            <!-- foo-bar -->',
                '',
                '    .. code-block:: php',
                '',
                '        namespace App\Entity;',
            ], 6),
        ];
        yield [
            NullViolation::create(),
            new RstSample([
                'Welcome to our tutorial!',
                '',
                '     .. code-block:: php',
                '         :option: foo',
                '',
                '         namespace App\Entity;',
            ], 3),
        ];
    }

    /**
     * @return \Generator<int|string, array{0: ViolationInterface, 1: RstSample}>
     */
    public static function realSymfonyFileProvider(): iterable
    {
        $content = <<<'RST'
.. configuration-block::

    .. code-block:: yaml

        # app/config/services.yml
        services:
            app.mailer:
                class:        AppBundle\Mailer
                arguments:    [sendmail]

    .. code-block:: xml

        <!-- app/config/services.xml -->
        <?xml version="1.0" encoding="UTF-8" ?>
        <container xmlns="http://symfony.com/schema/dic/services"
            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xsi:schemaLocation="http://symfony.com/schema/dic/services
                http://symfony.com/schema/dic/services/services-1.0.xsd">

            <services>
                <service id="app.mailer" class="AppBundle\Mailer">
                    <argument>sendmail</argument>
                </service>
            </services>
        </container>

    .. code-block:: php

        // app/config/services.php
        use AppBundle\Mailer;

        $container->register('app.mailer', Mailer::class)
            ->addArgument('sendmail');

RST;

        $content_with_blank_line_at_the_beginning = <<<'RST'

.. configuration-block::

    .. code-block:: yaml

        # app/config/services.yml
        services:
            app.mailer:
                class:        AppBundle\Mailer
                arguments:    [sendmail]

    .. code-block:: xml

        <!-- app/config/services.xml -->
        <?xml version="1.0" encoding="UTF-8" ?>
        <container xmlns="http://symfony.com/schema/dic/services"
            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xsi:schemaLocation="http://symfony.com/schema/dic/services
                http://symfony.com/schema/dic/services/services-1.0.xsd">

            <services>
                <service id="app.mailer" class="AppBundle\Mailer">
                    <argument>sendmail</argument>
                </service>
            </services>
        </container>

    .. code-block:: php

        // app/config/services.php
        use AppBundle\Mailer;

        $container->register('app.mailer', Mailer::class)
            ->addArgument('sendmail');

RST;

        $invalid_content =

        $valid_code_block_after_headline = <<<'RST'
Creating an ACL and Adding an ACE
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. code-block:: php

    // src/AppBundle/Controller/BlogController.php
    namespace AppBundle\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\Security\Core\Exception\AccessDeniedException;
    use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
    use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
    use Symfony\Component\Security\Acl\Permission\MaskBuilder;

    class BlogController extends Controller
    {
RST;

        $valid_two_following_php_code_blocks = <<<'RST'
How to Reduce Code Duplication with "inherit_data"
==================================================

The ``inherit_data`` form field option can be very useful when you have some
duplicated fields in different entities. For example, imagine you have two
entities, a ``Company`` and a ``Customer``::

    // src/AppBundle/Entity/Company.php
    namespace AppBundle\Entity;

    class Company
    {
        private $name;
    }

.. code-block:: php

    // src/AppBundle/Entity/Customer.php
    namespace AppBundle\Entity;

    class Customer
    {
        private $firstName;
    }
RST;

        $valid_two_following_php_code_blocks_after_headline = <<<'RST'
How to Reduce Code Duplication with "inherit_data"
==================================================

.. code-block:: php

    // src/AppBundle/Entity/Company.php
    namespace AppBundle\Entity;

    class Company
    {
        private $name;
    }

.. code-block:: php

    // src/AppBundle/Entity/Customer.php
    namespace AppBundle\Entity;

    class Customer
    {
        private $firstName;
    }
RST;

        $valid_two_following_php_code_blocks_in_configuration_block = <<<'RST'
Test
====

.. configuration-block::

    .. code-block:: php

        // src/AppBundle/Entity/Company.php
        namespace AppBundle\Entity;

        class Company
        {
            private $name;
        }

    .. code-block:: php

        // src/AppBundle/Entity/Customer.php
        namespace AppBundle\Entity;

        class Customer
        {
            private $firstName;
        }
RST;

        $valid_valid_in_code_block_text = <<<'RST'
Example
~~~~~~~

.. code-block:: text

    Example
    =======

    When you are working on the docs, you should follow the
    `Symfony Documentation`_ standards.

    Level 2
    -------

    A PHP example would be::

        echo 'Hello World';

    Level 3
    ~~~~~~~

    .. code-block:: php

        echo 'You cannot use the :: shortcut here';

    .. _`Symfony Documentation`: https://symfony.com/doc
RST;

        $valid_valid_in_code_block_rst = <<<'RST'
Example
~~~~~~~

.. code-block:: rst

    Example
    =======

    When you are working on the docs, you should follow the
    `Symfony Documentation`_ standards.

    Level 2
    -------

    A PHP example would be::

        echo 'Hello World';

    Level 3
    ~~~~~~~

    .. code-block:: php

        echo 'You cannot use the :: shortcut here';

    .. _`Symfony Documentation`: https://symfony.com/doc
RST;

        $valid_follows_code_block = <<<'RST'
The second argument of the :method:`Symfony\\Component\\Yaml\\Yaml::dump`
method customizes the level at which the output switches from the expanded
representation to the inline one::

    echo Yaml::dump($array, 1);

.. code-block:: yaml

    foo: bar
    bar: { foo: bar, bar: baz }

.. code-block:: php

    echo Yaml::dump($array, 2);

.. code-block:: yaml

    foo: bar
    bar:
        foo: bar
        bar: baz
RST;

        $valid_code_block_after_table = <<<'RST'
You can use the following parameters:

======================================  ============================================================
Parameter                               Description
======================================  ============================================================
**choices**                             Array of choices
**required**                            Whether the field is required or not (default true) when the
                                        ``editable`` option is set to ``true``. If false, an empty
                                        placeholder will be added.
======================================  ============================================================

.. code-block:: php

    protected function configureListFields(ListMapper $listMapper)
    {
        // For the value `prog`, the displayed text is `In progress`. The `App` catalogue will be used to translate `In progress` message.
        $listMapper
            ->add('status', 'choice', [
                'choices' => [
                    'prep' => 'Prepared',
                    'prog' => 'In progress',
                    'done' => 'Done',
                ],
                'catalogue' => 'App',
            ])
        ;
    }
RST;

        yield [
            NullViolation::create(),
            new RstSample($content, 26),
        ];

        yield [
            NullViolation::create(),
            new RstSample($content_with_blank_line_at_the_beginning, 27),
        ];

        yield [
            Violation::from(
                'Please do not use ".. code-block:: php", use "::" instead.',
                'filename',
                15,
                '.. code-block:: php',
            ),
            new RstSample(
                <<<'RST'
.. configuration-block::

    .. code-block:: yaml

        # app/config/services.yml
        services:
            app.mailer:
                class:        AppBundle\Mailer
                arguments:    [sendmail]

.. note::

    Try to use it like this:

    .. code-block:: php

        echo 'foo';

RST,
                14,
            ),
        ];

        yield [
            NullViolation::create(),
            new RstSample($valid_code_block_after_headline, 3),
        ];

        yield [
            NullViolation::create(),
            new RstSample($valid_two_following_php_code_blocks, 15),
        ];

        yield [
            NullViolation::create(),
            new RstSample($valid_two_following_php_code_blocks_after_headline, 2),
        ];

        yield [
            NullViolation::create(),
            new RstSample($valid_two_following_php_code_blocks_after_headline, 13),
        ];

        yield [
            NullViolation::create(),
            new RstSample($valid_two_following_php_code_blocks_in_configuration_block, 5),
        ];

        yield [
            NullViolation::create(),
            new RstSample($valid_two_following_php_code_blocks_in_configuration_block, 15),
        ];

        yield [
            NullViolation::create(),
            new RstSample($valid_valid_in_code_block_text, 21),
        ];

        yield [
            NullViolation::create(),
            new RstSample($valid_valid_in_code_block_rst, 21),
        ];
        yield [
            NullViolation::create(),
            new RstSample($valid_follows_code_block, 11),
        ];

        yield 'valid_code_block_after_table' => [
            NullViolation::create(),
            new RstSample($valid_code_block_after_table, 11),
        ];

        yield [
            Violation::from(
                'Please do not use ".. code-block:: php", use "::" instead.',
                'filename',
                19,
                '.. code-block:: php',
            ),
            new RstSample(
                <<<'RST'
label_translation_parameters
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

**type**: ``array`` **default**: ``[]``

The content of the `label`_ option is translated before displaying it, so it
can contain :ref:`translation placeholders <component-translation-placeholders>`.
This option defines the values used to replace those placeholders.

Given this translation message:

.. code-block:: yaml

    # translations/messages.en.yaml
    form.order.reset: 'Reset an order to %company%'

You can specify the placeholder values as follows:

.. code-block:: php

    use Symfony\Component\Form\Extension\Core\Type\ResetType;
    // ...

    $builder->add('send', ResetType::class, [
        'label' => 'form.order.reset',
        'label_translation_parameters' => [
            '%company%' => 'ACME Inc.',
        ],
    ]);

The ``label_translation_parameters`` option of buttons is merged with the same
option of its parents, so buttons can reuse and/or override any of the parent
placeholders.
RST,
                18,
            ),
        ];

        yield 'valid because previous paragraph ends with question mark (?)' => [
            NullViolation::create(),
            new RstSample(<<<'RST'
This is nice PHP code, isn't it?

.. code-block:: php

    echo 'Hello World!';
RST
                , 2),
        ];

        yield 'php code block following a configuration-block' => [
            NullViolation::create(),
            new RstSample(<<<'RST'
.. configuration-block::

    .. code-block:: xml

        content1

.. code-block:: php

    echo 'Hello World!';
RST
                , 6),
        ];

        yield 'php code block following a terminal block' => [
            NullViolation::create(),
            new RstSample(<<<'RST'
.. code-block:: terminal

    $ php bin/console make:user

.. code-block:: php

    echo 'Hello World!';
    }
RST
                , 4),
        ];

        yield 'php code block unsing an option' => [
            NullViolation::create(),
            new RstSample(<<<'RST'
.. code-block:: php
   :lineos:

    echo 'Hello World!';
RST
                , 1),
        ];

        foreach (NoExplicitUseOfCodeBlockPhp::ALLOWED_PREVIOUS_DIRECTIVES as $previousDirective) {
            yield \sprintf(
                'php code block following %s',
                $previousDirective,
            ) => [
                NullViolation::create(),
                new RstSample(\sprintf(<<<'RST'
%s

    Here is text.

.. code-block:: php

    echo 'Hello World!';
RST, $previousDirective), 4),
            ];
        }
    }
}
