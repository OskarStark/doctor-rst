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

use App\Rule\EnsureCorrectFormatForPhpfunction;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class EnsureCorrectFormatForPhpfunctionTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        $rule = (new EnsureCorrectFormatForPhpfunction());

        self::assertEquals($expected, $rule->check($sample->lines, $sample->lineNumber, 'filename'));
    }

    /**
     * @return \Generator<array{0: ViolationInterface, 1: RstSample}>
     */
    public static function checkProvider(): iterable
    {
        yield [
            NullViolation::create(),
            new RstSample('This option accepts any value that can be passed to :phpfunction:`mb_detect_encoding`.'),
        ];
        yield [
            NullViolation::create(),
            new RstSample('Some common functions as :phpfunction:`empty` or :phpfunction:`isset`.'),
        ];
        yield [
            Violation::from(
                'Please do not use () at the end of PHP function',
                'filename',
                1,
                'This option accepts any value that can be passed to :phpfunction:`mb_detect_encoding()`.',
            ),
            new RstSample('This option accepts any value that can be passed to :phpfunction:`mb_detect_encoding()`.'),
        ];
        yield [
            Violation::from(
                'Please do not use () at the end of PHP function',
                'filename',
                1,
                'Some common functions as :phpfunction:`empty()` or :phpfunction:`isset()`.',
            ),
            new RstSample('Some common functions as :phpfunction:`empty()` or :phpfunction:`isset()`.'),
        ];
    }
}
