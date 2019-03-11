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

use App\Rule\BlankLineAfterFilepathInCodeBlock;
use PHPUnit\Framework\TestCase;

class BlankLineAfterFilepathInCodeBlockTest extends TestCase
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
    public function check($expected, $line)
    {
        $this->assertSame(
            $expected,
            (new BlankLineAfterFilepathInCodeBlock())->check(new \ArrayIterator(\is_array($line) ? $line : [$line]), 0)
        );
    }

    public function checkProvider()
    {
        return [
            [
                null,
                'temp',
            ],
        ];
    }

    public function checkPhpProvider()
    {
        return [
            [
                'Please add a blank line after "// src/Handler/Collection.php"',
                [
                    '.. code-block:: php',
                    '',
                    '// src/Handler/Collection.php',
                    'namespace App\\Handler;',
                ],
            ],
            [
                null,
                [
                    '.. code-block:: php',
                    '',
                    '// src/Handler/Collection.php',
                    '',
                    'namespace App\\Handler;',
                ],
            ],
        ];
    }

    public function checkYmlProvider()
    {
        return [
            [
                'Please add a blank line after "# config/services.yml"',
                [
                    '.. code-block:: yml',
                    '',
                    '# config/services.yml',
                    'services:',
                ],
            ],
            [
                null,
                [
                    '.. code-block:: yml',
                    '',
                    '# config/services.yml',
                    '',
                    'services:',
                ],
            ],
        ];
    }

    public function checkYamlProvider()
    {
        return [
            [
                'Please add a blank line after "# config/services.yaml"',
                [
                    '.. code-block:: yaml',
                    '',
                    '# config/services.yaml',
                    'services:',
                ],
            ],
            [
                null,
                [
                    '.. code-block:: yaml',
                    '',
                    '# config/services.yaml',
                    '',
                    'services:',
                ],
            ],
        ];
    }

    public function checkXmlProvider()
    {
        return [
            [
                'Please add a blank line after "<!-- config/services.xml -->"',
                [
                    '.. code-block:: xml',
                    '',
                    '<!-- config/services.xml -->',
                    '<foo\/>',
                ],
            ],
            [
                null,
                [
                    '.. code-block:: xml',
                    '',
                    '<!-- config/services.xml -->',
                    '',
                    '<foo\/>',
                ],
            ],
            [
                'Please add a blank line after "<!--config/services.xml-->"',
                [
                    '.. code-block:: xml',
                    '',
                    '<!--config/services.xml-->',
                    '<foo\/>',
                ],
            ],
            [
                null,
                [
                    '.. code-block:: xml',
                    '',
                    '<!--config/services.xml-->',
                    '',
                    '<foo\/>',
                ],
            ],
        ];
    }

    public function checkTwigProvider()
    {
        return [
            [
                'Please add a blank line after "{# templates/index.html.twig #}"',
                [
                    '.. code-block:: twig',
                    '',
                    '{# templates/index.html.twig #}',
                    '{% set foo = "bar" %}',
                ],
            ],
            [
                null,
                [
                    '.. code-block:: twig',
                    '',
                    '{# templates/index.html.twig #}',
                    '',
                    '{% set foo = "bar" %}',
                ],
            ],
            [
                'Please add a blank line after "{# templates/index.html.twig #}"',
                [
                    '.. code-block:: jinja',
                    '',
                    '{# templates/index.html.twig #}',
                    '{% set foo = "bar" %}',
                ],
            ],
            [
                null,
                [
                    '.. code-block:: jinja',
                    '',
                    '{# templates/index.html.twig #}',
                    '',
                    '{% set foo = "bar" %}',
                ],
            ],
            [
                'Please add a blank line after "{# templates/index.html.twig #}"',
                [
                    '.. code-block:: html+jinja',
                    '',
                    '{# templates/index.html.twig #}',
                    '{% set foo = "bar" %}',
                ],
            ],
            [
                null,
                [
                    '.. code-block:: html+jinja',
                    '',
                    '{# templates/index.html.twig #}',
                    '',
                    '{% set foo = "bar" %}',
                ],
            ],
        ];
    }
}
