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

use App\Rule\OnlyBackslashesInNamespaceInPhpCodeBlock;
use App\Tests\RstSample;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;

final class OnlyBackslashesInNamespaceInPhpCodeBlockTest extends \App\Tests\UnitTestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        static::assertEquals(
            $expected,
            (new OnlyBackslashesInNamespaceInPhpCodeBlock())->check($sample->lines(), $sample->lineNumber(), 'filename')
        );
    }

    public function checkProvider(): \Generator
    {
        foreach (self::phpCodeBlocks() as $codeBlock) {
            yield [
                Violation::from(
                    'Please check "namespace App/Handler;", it should not contain "/"',
                    'filename',
                    1,
                    ''
                ),
                new RstSample([
                    $codeBlock,
                    '',
                    '    // src/Handler/Collection.php',
                    '',
                    '    namespace App/Handler;',
                ], 4),
            ];

            yield [
                Violation::from(
                    'Please check "NaMeSpaCe App/Handler;", it should not contain "/"',
                    'filename',
                    1,
                    ''
                ),
                new RstSample([
                    $codeBlock,
                    '',
                    '    // src/Handler/Collection.php',
                    '',
                    '    NaMeSpaCe App/Handler;',
                ], 4),
            ];
        }

        yield [
            Violation::from(
                'Please check "namespace App/Handler;", it should not contain "/"',
                'filename',
                1,
                ''
            ),
            new RstSample([
                '::',
                '',
                '    // src/Handler/Collection.php',
                '',
                '    namespace App/Handler;',
            ], 4),
        ];

        yield [
            NullViolation::create(),
            new RstSample('temp'),
        ];
    }
}
