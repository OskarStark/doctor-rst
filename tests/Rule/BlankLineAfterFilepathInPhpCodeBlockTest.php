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

use App\Rst\RstParser;
use App\Rule\BlankLineAfterFilepathInPhpCodeBlock;
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;

final class BlankLineAfterFilepathInPhpCodeBlockTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check(?string $expected, RstSample $sample): void
    {
        static::assertSame(
            $expected,
            (new BlankLineAfterFilepathInPhpCodeBlock())->check($sample->lines(), $sample->lineNumber())
        );
    }

    /**
     * @return \Generator<array{0: string|null, 1: RstSample}>
     */
    public function checkProvider(): \Generator
    {
        foreach (RstParser::PHP_CODE_BLOCKS as $codeBlock) {
            yield [
                'Please add a blank line after "// src/Handler/Collection.php"',
                new RstSample(
                    [
                        '.. code-block:: '.$codeBlock,
                        '',
                        '    // src/Handler/Collection.php',
                        '    namespace App\\Handler;',
                    ]
                ),
            ];

            yield [
                null,
                new RstSample(
                    [
                        '.. code-block:: '.$codeBlock,
                        '',
                        '    // src/Handler/Collection.php',
                        '',
                        '    namespace App\\Handler;',
                    ]
                ),
            ];

            yield [
                null,
                new RstSample(
                    [
                        '.. code-block:: '.$codeBlock,
                        '',
                        '    // src/Handler/Collection.php',
                        '    // a comment',
                        '    namespace App\\Handler;',
                    ]
                ),
            ];

            yield [
                null,
                new RstSample(
                    [
                        '.. code-block:: '.$codeBlock,
                        '',
                        '    // src/Handler/Collection.php',
                        '    # a comment',
                        '    namespace App\\Handler;',
                    ]
                ),
            ];
        }

        yield [
            null,
            new RstSample('temp'),
        ];
    }
}
