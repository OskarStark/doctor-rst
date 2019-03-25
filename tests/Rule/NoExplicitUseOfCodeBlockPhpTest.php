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

use App\Rule\NoExplicitUseOfCodeBlockPhp;
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;

class NoExplicitUseOfCodeBlockPhpTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     * @dataProvider realSymfonyFileProvider
     */
    public function check($expected, RstSample $sample)
    {
        $this->assertSame(
            $expected,
            (new NoExplicitUseOfCodeBlockPhp())->check($sample->getContent(), $sample->getLineNumber())
        );
    }

    public function checkProvider()
    {
        return [
            [
                null,
                new RstSample('Check the following controller syntax::'),
            ],
            [
                'Please do not use ".. code-block:: php", use "::" instead.',
                new RstSample('.. code-block:: php'),
            ],
            [
                null,
                new RstSample('.. code-block:: html+php'),
            ],
            [
                'Please do not use ".. code-block:: php", use "::" instead.',
                new RstSample('    .. code-block:: php'),
            ],
            [
                'Please do not use ".. code-block:: php", use "::" instead.',
                new RstSample([
                    'Welcome to our tutorial!',
                    '',
                    '     .. code-block:: php',
                    '',
                    'namespace App\Entity;',
                ], 2),
            ],
            [
                null,
                new RstSample([
                    '.. configuration-block::',
                    '',
                    ' .. code-block:: php',
                    '',
                    '  namespace App\Entity;',
                ], 2),
            ],
            [
                'Please do not use ".. code-block:: php", use "::" instead.',
                new RstSample([
                    '    .. configuration-block::',
                    '',
                    '        .. code-block:: xml',
                    '',
                    '            <!-- foo-bar -->',
                    '',
                    '    .. code-block:: php',
                    '',
                    'namespace App\Entity;',
                ], 6),
            ],
        ];
    }

    public function realSymfonyFileProvider()
    {
        $content = <<<'CONTENT'
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

CONTENT;

        $content_with_blank_line_at_the_beginning = <<<'CONTENT'

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

CONTENT;

        $invalid_content = <<<CONTENT
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

CONTENT;

        $valid_code_block_after_headline = <<<'CONTENT'
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
CONTENT;

        $valid_two_following_php_code_blocks = <<<'CONTENT'
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
CONTENT;

        $valid_two_following_php_code_blocks_after_headline = <<<'CONTENT'
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
CONTENT;

        $valid_two_following_php_code_blocks_in_configuration_block = <<<'CONTENT'
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
CONTENT;

        $valid_valid_in_code_block_text = <<<'CONTENT'
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
CONTENT;

        $valid_valid_in_code_block_rst = <<<'CONTENT'
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
CONTENT;

        $valid_follows_code_block = <<<'CONTENT'
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
CONTENT;

        $valid_code_block_after_table = <<<'CONTENT'
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
CONTENT;

        return [
            [
                null,
                new RstSample($content, 26),
            ],
            [
                null,
                new RstSample($content_with_blank_line_at_the_beginning, 27),
            ],
            [
                'Please do not use ".. code-block:: php", use "::" instead.',
                new RstSample($invalid_content, 14),
            ],
            [
                null,
                new RstSample($valid_code_block_after_headline, 3),
            ],
            [
                null,
                new RstSample($valid_two_following_php_code_blocks, 15),
            ],
            [
                null,
                new RstSample($valid_two_following_php_code_blocks_after_headline, 2),
            ],
            [
                null,
                new RstSample($valid_two_following_php_code_blocks_after_headline, 13),
            ],
            [
                null,
                new RstSample($valid_two_following_php_code_blocks_in_configuration_block, 5),
            ],
            [
                null,
                new RstSample($valid_two_following_php_code_blocks_in_configuration_block, 15),
            ],
            [
                null,
                new RstSample($valid_valid_in_code_block_text, 21),
            ],
            [
                null,
                new RstSample($valid_valid_in_code_block_rst, 21),
            ],
            [
                null,
                new RstSample($valid_follows_code_block, 11),
            ],
            'valid_code_block_after_table' => [
                null,
                new RstSample($valid_code_block_after_table, 11),
            ],
        ];
    }
}
