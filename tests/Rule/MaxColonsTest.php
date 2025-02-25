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

use App\Rule\MaxColons;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class MaxColonsTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new MaxColons())->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    /**
     * @return \Generator<array{0: ViolationInterface, 1: RstSample}>
     */
    public static function checkProvider(): iterable
    {
        yield [
            NullViolation::create(),
            new RstSample('temp'),
        ];

        yield [
            NullViolation::create(),
            new RstSample('temp:'),
        ];

        yield [
            NullViolation::create(),
            new RstSample('temp::'),
        ];

        yield [
            Violation::from(
                'Please use max 2 colons at the end.',
                'filename',
                1,
                'temp:::',
            ),
            new RstSample('temp:::'),
        ];

        yield [
            Violation::from(
                'Please use max 2 colons at the end.',
                'filename',
                1,
                'temp:::',
            ),
            new RstSample(' temp:::'),
        ];

        yield [
            Violation::from(
                'Please use max 2 colons at the end.',
                'filename',
                1,
                'temp:::',
            ),
            new RstSample(' temp::: '),
        ];
    }
}
