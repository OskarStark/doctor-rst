<?php

declare(strict_types=1);

/**
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
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class OnlyBackslashesInUseStatementsInPhpCodeBlockTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new OnlyBackslashesInUseStatementsInPhpCodeBlock())->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    public static function checkProvider(): iterable
    {
        foreach (self::phpCodeBlocks() as $codeBlock) {
            yield [
                Violation::from(
                    'Please check "use App/Handler;", it should not contain "/"',
                    'filename',
                    5,
                    'use App/Handler;',
                ),
                new RstSample([
                    $codeBlock,
                    '',
                    '    // src/Handler/Collection.php',
                    '',
                    '    use App/Handler;',
                ], 4),
            ];

            yield [
                Violation::from(
                    'Please check "UsE App/Handler;", it should not contain "/"',
                    'filename',
                    5,
                    'UsE App/Handler;',
                ),
                new RstSample([
                    $codeBlock,
                    '',
                    '    // src/Handler/Collection.php',
                    '',
                    '    UsE App/Handler;',
                ], 4),
            ];
        }

        yield [
            Violation::from(
                'Please check "use App/Handler;", it should not contain "/"',
                'filename',
                5,
                'use App/Handler;',
            ),
            new RstSample([
                '::',
                '',
                '    // src/Handler/Collection.php',
                '',
                '    use App/Handler;',
            ], 4),
        ];

        yield [
            NullViolation::create(),
            new RstSample('temp'),
        ];
    }
}
