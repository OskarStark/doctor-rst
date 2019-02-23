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

namespace app\tests\Rule;

use App\Rule\NoBlankLineAfterFilepathInCodeBlock;
use PHPUnit\Framework\TestCase;

class NoBlankLineAfterFilepathInCodeBlockTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     * @dataProvider checkPhpProvider
     * @dataProvider checkYmlProvider
     * @dataProvider checkYamlProvider
     * @dataProvider checkXmlProvider
     */
    public function check($expected, $line)
    {
        $this->assertSame(
            $expected,
            (new NoBlankLineAfterFilepathInCodeBlock())->check(new \ArrayIterator(\is_array($line) ? $line : [$line]), 0)
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
                'Please remove blank line after "// src/Handler/Collection.php"',
                [
                    '.. code-block:: php',
                    '',
                    '// src/Handler/Collection.php',
                    '',
                    'namespace App\\Handler;',
                ],
            ],
            [
                null,
                [
                    '.. code-block:: php',
                    '',
                    '// src/Handler/Collection.php',
                    'namespace App\\Handler;',
                ],
            ],
        ];
    }

    public function checkYmlProvider()
    {
        return [
            [
                'Please remove blank line after "# config/services.yml"',
                [
                    '.. code-block:: yml',
                    '',
                    '# config/services.yml',
                    '',
                    'services:',
                ],
            ],
            [
                null,
                [
                    '.. code-block:: yml',
                    '',
                    '# config/services.yml',
                    'services:',
                ],
            ],
        ];
    }

    public function checkYamlProvider()
    {
        return [
            [
                'Please remove blank line after "# config/services.yaml"',
                [
                    '.. code-block:: yaml',
                    '',
                    '# config/services.yaml',
                    '',
                    'services:',
                ],
            ],
            [
                null,
                [
                    '.. code-block:: yaml',
                    '',
                    '# config/services.yaml',
                    'services:',
                ],
            ],
        ];
    }

    public function checkXmlProvider()
    {
        return [
            [
                'Please remove blank line after "<!-- config/services.xml -->"',
                [
                    '.. code-block:: xml',
                    '',
                    '<!-- config/services.xml -->',
                    '',
                    '<foo\/>',
                ],
            ],
            [
                null,
                [
                    '.. code-block:: xml',
                    '',
                    '<!-- config/services.xml -->',
                    '<foo\/>',
                ],
            ],
            [
                'Please remove blank line after "<!--config/services.xml-->"',
                [
                    '.. code-block:: xml',
                    '',
                    '<!--config/services.xml-->',
                    '',
                    '<foo\/>',
                ],
            ],
            [
                null,
                [
                    '.. code-block:: xml',
                    '',
                    '<!--config/services.xml-->',
                    '<foo\/>',
                ],
            ],
        ];
    }
}
