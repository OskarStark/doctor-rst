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
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;

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
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        static::assertEquals(
            $expected,
            (new NoBlankLineAfterFilepathInCodeBlock())->check($sample->lines(), $sample->lineNumber(), 'filename')
        );
    }

    public function checkProvider(): array
    {
        return [
            [
                NullViolation::create(),
                new RstSample('temp'),
            ],
        ];
    }

    public function checkPhpProvider(): array
    {
        return [
            [
                Violation::from(
                    'Please remove blank line after "// src/Handler/Collection.php"',
                    'filename',
                    3,
                    '// src/Handler/Collection.php',
                ),
                new RstSample([
                    '.. code-block:: php',
                    '',
                    '    // src/Handler/Collection.php',
                    '',
                    '    namespace App\\Handler;',
                ]),
            ],
            [
                NullViolation::create(),
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
                Violation::from(
                    'Please remove blank line after "# config/services.yml"',
                    'filename',
                    3,
                    '# config/services.yml',
                ),
                new RstSample([
                    '.. code-block:: yml',
                    '',
                    '    # config/services.yml',
                    '',
                    '    services:',
                ]),
            ],
            [
                NullViolation::create(),
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
                Violation::from(
                    'Please remove blank line after "# config/services.yaml"',
                    'filename',
                    3,
                    '# config/services.yaml',
                ),
                new RstSample([
                    '.. code-block:: yaml',
                    '',
                    '    # config/services.yaml',
                    '',
                    '    services:',
                ]),
            ],
            [
                NullViolation::create(),
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
                Violation::from(
                    'Please remove blank line after "<!-- config/services.xml -->"',
                    'filename',
                    3,
                    '<!-- config/services.xml -->',
                ),
                new RstSample([
                    '.. code-block:: xml',
                    '',
                    '    <!-- config/services.xml -->',
                    '',
                    '    <foo\/>',
                ]),
            ],
            [
                NullViolation::create(),
                new RstSample([
                    '.. code-block:: xml',
                    '',
                    '    <!-- config/services.xml -->',
                    '    <foo\/>',
                ]),
            ],
            [
                Violation::from(
                    'Please remove blank line after "<!--config/services.xml-->"',
                    'filename',
                    3,
                    '<!--config/services.xml-->',
                ),
                new RstSample([
                    '.. code-block:: xml',
                    '',
                    '    <!--config/services.xml-->',
                    '',
                    '    <foo\/>',
                ]),
            ],
            [
                NullViolation::create(),
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
                Violation::from(
                    'Please remove blank line after "{# templates/index.html.twig #}"',
                    'filename',
                    3,
                    '{# templates/index.html.twig #}',
                ),
                new RstSample([
                    '.. code-block:: twig',
                    '',
                    '    {# templates/index.html.twig #}',
                    '',
                    '    {% set foo = "bar" %}',
                ]),
            ],
            [
                NullViolation::create(),
                new RstSample([
                    '.. code-block:: twig',
                    '',
                    '    {# templates/index.html.twig #}',
                    '    {% set foo = "bar" %}',
                ]),
            ],
            [
                Violation::from(
                    'Please remove blank line after "{# templates/index.html.twig #}"',
                    'filename',
                    3,
                    '{# templates/index.html.twig #}',
                ),
                new RstSample([
                    '.. code-block:: jinja',
                    '',
                    '    {# templates/index.html.twig #}',
                    '',
                    '    {% set foo = "bar" %}',
                ]),
            ],
            [
                NullViolation::create(),
                new RstSample([
                    '.. code-block:: jinja',
                    '',
                    '    {# templates/index.html.twig #}',
                    '    {% set foo = "bar" %}',
                ]),
            ],
            [
                Violation::from(
                    'Please remove blank line after "{# templates/index.html.twig #}"',
                    'filename',
                    3,
                    '{# templates/index.html.twig #}',
                ),
                new RstSample([
                    '.. code-block:: html+jinja',
                    '',
                    '    {# templates/index.html.twig #}',
                    '',
                    '    {% set foo = "bar" %}',
                ]),
            ],
            [
                NullViolation::create(),
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
