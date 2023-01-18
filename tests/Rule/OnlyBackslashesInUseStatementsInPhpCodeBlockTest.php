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

use App\Rule\OnlyBackslashesInUseStatementsInPhpCodeBlock;
use App\Tests\RstSample;

final class OnlyBackslashesInUseStatementsInPhpCodeBlockTest extends \App\Tests\UnitTestCase
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
            (new OnlyBackslashesInUseStatementsInPhpCodeBlock())->check($sample->lines(), $sample->lineNumber())
        );
    }

    public function checkProvider(): \Generator
    {
        foreach (self::phpCodeBlocks() as $codeBlock) {
            yield [
                'Please check "use App/Handler;", it should not contain "/"',
                new RstSample([
                    $codeBlock,
                    '',
                    '    // src/Handler/Collection.php',
                    '',
                    '    use App/Handler;',
                ], 4),
            ];

            yield [
                'Please check "UsE App/Handler;", it should not contain "/"',
                new RstSample([
                    '.. code-block:: '.$codeBlock,
                    '',
                    '    // src/Handler/Collection.php',
                    '',
                    '    UsE App/Handler;',
                ], 4),
            ];
        }

        yield [
            'Please check "use App/Handler;", it should not contain "/"',
            new RstSample([
                '::',
                '',
                '    // src/Handler/Collection.php',
                '',
                '    use App/Handler;',
            ], 4),
        ];

        yield [
            null,
            new RstSample('temp'),
        ];
    }
}
