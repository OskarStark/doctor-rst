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

use App\Rule\NoBlankLineAfterFilepathInPhpCodeBlock;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class NoBlankLineAfterFilepathInPhpCodeBlockTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new NoBlankLineAfterFilepathInPhpCodeBlock())->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    public static function checkProvider(): iterable
    {
        foreach (self::phpCodeBlocks() as $codeBlock) {
            yield [
                Violation::from(
                    'Please remove blank line after "// src/Handler/Collection.php"',
                    'filename',
                    3,
                    '// src/Handler/Collection.php',
                ),
                new RstSample([
                    $codeBlock,
                    '',
                    '    // src/Handler/Collection.php',
                    '',
                    '    namespace App\\Handler;',
                ]),
            ];

            yield [
                NullViolation::create(),
                new RstSample([
                    $codeBlock,
                    '',
                    '    // src/Handler/Collection.php',
                    '    namespace App\\Handler;',
                ]),
            ];

            yield [
                NullViolation::create(),
                new RstSample([
                    $codeBlock,
                    '',
                    '    // src/Handler/Collection.php',
                    '',
                    '    // a comment',
                    '    namespace App\\Handler;',
                ]),
            ];

            yield [
                NullViolation::create(),
                new RstSample([
                    $codeBlock,
                    '',
                    '    // src/Handler/Collection.php',
                    '',
                    '    # a comment',
                    '    namespace App\\Handler;',
                ]),
            ];
        }

        yield [
            NullViolation::create(),
            new RstSample('temp'),
        ];
    }
}
