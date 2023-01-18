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

use App\Rule\NoBlankLineAfterFilepathInCodeBlock;
use App\Tests\RstSample;

final class NoBlankLineAfterFilepathInCodeBlockTest extends \App\Tests\UnitTestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     * @dataProvider checkPhpProvider
     * @dataProvider checkYmlProvider
     * @dataProvider checkYamlProvider
     * @dataProvider checkXmlProvider
     * @dataProvider checkTwigProvider
     */
    public function check(?string $expected, RstSample $sample): void
    {
        static::assertSame(
            $expected,
            (new NoBlankLineAfterFilepathInCodeBlock())->check($sample->lines(), $sample->lineNumber())
        );
    }

    public function checkProvider(): array
    {
        return [
            [
                null,
                new RstSample('temp'),
            ],
        ];
    }

    public function checkPhpProvider(): array
    {
        return [
            [
                'Please remove blank line after "// src/Handler/Collection.php"',
                new RstSample([
                    '.. code-block:: php',
                    '',
                    '    // src/Handler/Collection.php',
                    '',
                    '    namespace App\\Handler;',
                ]),
            ],
            [
                null,
                new RstSample([
                    '.. code-block:: php',
                    '',
                    '    // src/Handler/Collection.php',
                    '    namespace App\\Handler;',
                ]),
            ],
        ];
    }

    public function checkYmlProvider(): array
    {
        return [
            [
                'Please remove blank line after "# config/services.yml"',
                new RstSample([
                    '.. code-block:: yml',
                    '',
                    '    # config/services.yml',
                    '',
                    '    services:',
                ]),
            ],
            [
                null,
                new RstSample([
                    '.. code-block:: yml',
                    '',
                    '    # config/services.yml',
                    '    services:',
                ]),
            ],
        ];
    }

    public function checkYamlProvider(): array
    {
        return [
            [
                'Please remove blank line after "# config/services.yaml"',
                new RstSample([
                    '.. code-block:: yaml',
                    '',
                    '    # config/services.yaml',
                    '',
                    '    services:',
                ]),
            ],
            [
                null,
                new RstSample([
                    '.. code-block:: yaml',
                    '',
                    '    # config/services.yaml',
                    '    services:',
                ]),
            ],
        ];
    }

    public function checkXmlProvider(): array
    {
        return [
            [
                'Please remove blank line after "<!-- config/services.xml -->"',
                new RstSample([
                    '.. code-block:: xml',
                    '',
                    '    <!-- config/services.xml -->',
                    '',
                    '    <foo\/>',
                ]),
            ],
            [
                null,
                new RstSample([
                    '.. code-block:: xml',
                    '',
                    '    <!-- config/services.xml -->',
                    '    <foo\/>',
                ]),
            ],
            [
                'Please remove blank line after "<!--config/services.xml-->"',
                new RstSample([
                    '.. code-block:: xml',
                    '',
                    '    <!--config/services.xml-->',
                    '',
                    '    <foo\/>',
                ]),
            ],
            [
                null,
                new RstSample([
                    '.. code-block:: xml',
                    '',
                    '    <!--config/services.xml-->',
                    '    <foo\/>',
                ]),
            ],
        ];
    }

    public function checkTwigProvider(): array
    {
        return [
            [
                'Please remove blank line after "{# templates/index.html.twig #}"',
                new RstSample([
                    '.. code-block:: twig',
                    '',
                    '    {# templates/index.html.twig #}',
                    '',
                    '    {% set foo = "bar" %}',
                ]),
            ],
            [
                null,
                new RstSample([
                    '.. code-block:: twig',
                    '',
                    '    {# templates/index.html.twig #}',
                    '    {% set foo = "bar" %}',
                ]),
            ],
            [
                'Please remove blank line after "{# templates/index.html.twig #}"',
                new RstSample([
                    '.. code-block:: jinja',
                    '',
                    '    {# templates/index.html.twig #}',
                    '',
                    '    {% set foo = "bar" %}',
                ]),
            ],
            [
                null,
                new RstSample([
                    '.. code-block:: jinja',
                    '',
                    '    {# templates/index.html.twig #}',
                    '    {% set foo = "bar" %}',
                ]),
            ],
            [
                'Please remove blank line after "{# templates/index.html.twig #}"',
                new RstSample([
                    '.. code-block:: html+jinja',
                    '',
                    '    {# templates/index.html.twig #}',
                    '',
                    '    {% set foo = "bar" %}',
                ]),
            ],
            [
                null,
                new RstSample([
                    '.. code-block:: html+jinja',
                    '',
                    '    {# templates/index.html.twig #}',
                    '    {% set foo = "bar" %}',
                ]),
            ],
        ];
    }
}
