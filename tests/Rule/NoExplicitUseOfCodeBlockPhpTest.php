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
use PHPUnit\Framework\TestCase;

class NoExplicitUseOfCodeBlockPhpTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     * @dataProvider realSymfonyFileProvider
     */
    public function check($expected, $line, $number = 0)
    {
        $this->assertSame(
            $expected,
            (new NoExplicitUseOfCodeBlockPhp())->check(new \ArrayIterator(\is_array($line) ? $line : [$line]), $number)
        );
    }

    public function checkProvider()
    {
        return [
            [
                null,
                'Check the following controller syntax::',
            ],
            [
                'Please do not use ".. code-block:: php", use "::" instead.',
                '.. code-block:: php',
            ],
            [
                null,
                '.. code-block:: html+php',
            ],
            [
                'Please do not use ".. code-block:: php", use "::" instead.',
                '    .. code-block:: php',
            ],
            [
                'Please do not use ".. code-block:: php", use "::" instead.',
                [
                    'Welcome to our tutorial!',
                    '',
                    '     .. code-block:: php',
                    '',
                    'namespace App\Entity;',
                ],
                2,
            ],
            [
                null,
                [
                    '.. configuration-block::',
                    '',
                    ' .. code-block:: php',
                    '',
                    '  namespace App\Entity;',
                ],
                2,
            ],
            [
                'Please do not use ".. code-block:: php", use "::" instead.',
                [
                    '    .. configuration-block::',
                    '',
                    '        .. code-block:: xml',
                    '',
                    '            <!-- foo-bar -->',
                    '',
                    '    .. code-block:: php',
                    '',
                    'namespace App\Entity;',
                ],
                6,
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

        $invalid_content = <<<'CONTENT'
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

        return [
            [
                null,
                explode(PHP_EOL, $content),
                26,
            ],
            [
                null,
                explode(PHP_EOL, $content_with_blank_line_at_the_beginning),
                27,
            ],
            [
                'Please do not use ".. code-block:: php", use "::" instead.',
                explode(PHP_EOL, $invalid_content),
                14,
            ],
        ];
    }
}
