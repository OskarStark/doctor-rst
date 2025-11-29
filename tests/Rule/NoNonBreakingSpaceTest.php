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

use App\Rule\NoNonBreakingSpace;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class NoNonBreakingSpaceTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new NoNonBreakingSpace())->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    /**
     * @return \Generator<array{0: ViolationInterface, 1: RstSample}>
     */
    public static function checkProvider(): iterable
    {
        $violationMessage = 'Please replace non-breaking or special whitespace characters with regular spaces';

        // Valid cases - regular spaces
        yield 'valid: regular space' => [
            NullViolation::create(),
            new RstSample('Valid sentence with regular spaces'),
        ];

        yield 'valid: empty line' => [
            NullViolation::create(),
            new RstSample(''),
        ];

        yield 'valid: only regular whitespace' => [
            NullViolation::create(),
            new RstSample('    indented line'),
        ];

        yield 'valid: tabs are allowed' => [
            NullViolation::create(),
            new RstSample("\tindented with tab"),
        ];

        // Invalid cases - non-breaking space (U+00A0)
        yield 'invalid: non-breaking space' => [
            Violation::from(
                $violationMessage,
                'filename',
                1,
                "Invalid\u{00A0}sentence",
            ),
            new RstSample("Invalid\u{00A0}sentence"),
        ];

        // Invalid cases - en space (U+2002)
        yield 'invalid: en space' => [
            Violation::from(
                $violationMessage,
                'filename',
                1,
                "Invalid\u{2002}sentence",
            ),
            new RstSample("Invalid\u{2002}sentence"),
        ];

        // Invalid cases - em space (U+2003)
        yield 'invalid: em space' => [
            Violation::from(
                $violationMessage,
                'filename',
                1,
                "Invalid\u{2003}sentence",
            ),
            new RstSample("Invalid\u{2003}sentence"),
        ];

        // Invalid cases - thin space (U+2009)
        yield 'invalid: thin space' => [
            Violation::from(
                $violationMessage,
                'filename',
                1,
                "Invalid\u{2009}sentence",
            ),
            new RstSample("Invalid\u{2009}sentence"),
        ];

        // Invalid cases - zero-width space (U+200B)
        yield 'invalid: zero-width space' => [
            Violation::from(
                $violationMessage,
                'filename',
                1,
                "Invalid\u{200B}sentence",
            ),
            new RstSample("Invalid\u{200B}sentence"),
        ];

        // Invalid cases - narrow no-break space (U+202F)
        yield 'invalid: narrow no-break space' => [
            Violation::from(
                $violationMessage,
                'filename',
                1,
                "Invalid\u{202F}sentence",
            ),
            new RstSample("Invalid\u{202F}sentence"),
        ];

        // Invalid cases - ideographic space (U+3000)
        yield 'invalid: ideographic space' => [
            Violation::from(
                $violationMessage,
                'filename',
                1,
                "Invalid\u{3000}sentence",
            ),
            new RstSample("Invalid\u{3000}sentence"),
        ];

        // Invalid cases - byte order mark (U+FEFF)
        // Note: BOM at beginning gets trimmed by Line->clean()
        yield 'invalid: byte order mark' => [
            Violation::from(
                $violationMessage,
                'filename',
                1,
                'Sentence with BOM',
            ),
            new RstSample("\u{FEFF}Sentence with BOM"),
        ];

        // Invalid at different positions
        // Note: Leading NBSP gets trimmed by Line->clean()
        yield 'invalid: non-breaking space at beginning' => [
            Violation::from(
                $violationMessage,
                'filename',
                1,
                'Start of line',
            ),
            new RstSample("\u{00A0}Start of line"),
        ];

        // Note: Trailing NBSP gets trimmed by Line->clean()
        yield 'invalid: non-breaking space at end' => [
            Violation::from(
                $violationMessage,
                'filename',
                1,
                'End of line',
            ),
            new RstSample("End of line\u{00A0}"),
        ];

        yield 'invalid: multiple non-breaking spaces' => [
            Violation::from(
                $violationMessage,
                'filename',
                1,
                "Multiple\u{00A0}invalid\u{00A0}spaces",
            ),
            new RstSample("Multiple\u{00A0}invalid\u{00A0}spaces"),
        ];
    }
}
