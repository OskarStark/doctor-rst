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

use App\Rule\NoAttributeRedundantParenthesis;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class NoAttributeRedundantParenthesisTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new NoAttributeRedundantParenthesis())->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    public static function checkProvider(): iterable
    {
        yield from [
            [
                Violation::from(
                    'Please remove redundant parenthesis on attribute',
                    'filename',
                    1,
                    '#[Bar()]',
                ),
                new RstSample('#[Bar()]'),
            ],
            [
                Violation::from(
                    'Please remove redundant parenthesis on attribute',
                    'filename',
                    1,
                    'Attribute #[Bar()] in my text',
                ),
                new RstSample('Attribute #[Bar()] in my text'),
            ],
            [
                NullViolation::create(),
                new RstSample('#[Bar]'),
            ],
            [
                NullViolation::create(),
                new RstSample("#[Bar('foo')]"),
            ],
        ];
    }
}
