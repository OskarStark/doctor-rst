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

use App\Rule\TitleUnderlineLengthMustMatchTitleLength;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class TitleUnderlineLengthMustMatchTitleLengthTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new TitleUnderlineLengthMustMatchTitleLength())->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    public static function checkProvider(): iterable
    {
        yield [
            Violation::from(
                \sprintf('Please ensure title "%s" and underline length are matching', 'Title with too short underline'),
                'filename',
                1,
                'Title with too short underline',
            ),
            new RstSample([
                'Title with too short underline',
                '~~~~~~~~~~~~~~~~~~~~~~~~',
            ], 1),
        ];

        yield [
            Violation::from(
                \sprintf('Please ensure title "%s" and underline length are matching', 'lowStrengthMessage'),
                'filename',
                1,
                '``lowStrengthMessage``',
            ),
            new RstSample([
                '``lowStrengthMessage``',
                '~~~~~~~~~~~',
            ], 1),
        ];

        yield [
            Violation::from(
                \sprintf('Please ensure title "%s" and underline length are matching', 'Title with too long underline'),
                'filename',
                1,
                'Title with too long underline',
            ),
            new RstSample([
                'Title with too long underline',
                '~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~',
            ], 1),
        ];

        yield [
            NullViolation::create(),
            new RstSample([
                't',
                '~',
            ], 1),
        ];

        yield [
            NullViolation::create(),
            new RstSample([
                'tt',
                '~~',
            ], 1),
        ];

        yield [
            NullViolation::create(),
            new RstSample([
                'Title with matching underline',
                '~~~~~~~~~~~~~~~~~~~~~~~~~~~~~',
            ], 1),
        ];
    }
}
