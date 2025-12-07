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

use App\Rule\EnsureClassConstant;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class EnsureClassConstantTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new EnsureClassConstant())->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    public static function checkProvider(): iterable
    {
        $codeBlocks = self::phpCodeBlocks();

        // VALID
        foreach ($codeBlocks as $codeBlock) {
            // WITH blank line after directive
            yield [
                NullViolation::create(),
                new RstSample([
                    $codeBlock,
                    '',
                    '    $foo = MyClass::class;',
                ]),
            ];

            // WITHOUT blank line after directive
            yield [
                NullViolation::create(),
                new RstSample([
                    $codeBlock,
                    '    $foo = MyClass::class;',
                ]),
            ];
        }

        // INVALID
        foreach ($codeBlocks as $codeBlock) {
            // WITH blank line after directive
            yield [
                Violation::from(
                    'Please use ::class constant over get_class()',
                    'filename',
                    3,
                    'get_class($foo);',
                ),
                new RstSample([
                    $codeBlock,
                    '',
                    '    get_class($foo);',
                ]),
            ];

            // WITHOUT blank line after directive
            yield [
                Violation::from(
                    'Please use ::class constant over get_class()',
                    'filename',
                    2,
                    'get_class($foo);',
                ),
                new RstSample([
                    $codeBlock,
                    '    get_class($foo);',
                ]),
            ];
        }
    }
}
