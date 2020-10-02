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

use App\Rule\NoBlankLineAfterFilepathInPhpCodeBlock;
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;

class NoBlankLineAfterFilepathInPhpCodeBlockTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check(?string $expected, RstSample $sample)
    {
        static::assertSame(
            $expected,
            (new NoBlankLineAfterFilepathInPhpCodeBlock())->check($sample->lines(), $sample->lineNumber())
        );
    }

    public function checkProvider()
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
            [
                null,
                new RstSample([
                    '.. code-block:: php',
                    '',
                    '    // src/Handler/Collection.php',
                    '',
                    '    // a comment',
                    '    namespace App\\Handler;',
                ]),
            ],
            [
                null,
                new RstSample([
                    '.. code-block:: php',
                    '',
                    '    // src/Handler/Collection.php',
                    '',
                    '    # a comment',
                    '    namespace App\\Handler;',
                ]),
            ],
            [
                null,
                new RstSample('temp'),
            ],
        ];
    }
}
