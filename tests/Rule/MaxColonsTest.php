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

use App\Rule\MaxColons;
use App\Tests\RstSample;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;

final class MaxColonsTest extends \App\Tests\UnitTestCase
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
            (new MaxColons())->check($sample->lines(), $sample->lineNumber(), 'filename')
        );
    }

    /**
     * @return \Generator<array{0: ViolationInterface, 1: RstSample}>
     */
    public function checkProvider(): \Generator
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
                'temp:::'
            ),
            new RstSample('temp:::'),
        ];

        yield [
            Violation::from(
                'Please use max 2 colons at the end.',
                'filename',
                1,
                'temp:::'
            ),
            new RstSample(' temp:::'),
        ];

        yield [
            Violation::from(
                'Please use max 2 colons at the end.',
                'filename',
                1,
                'temp:::'
            ),
            new RstSample(' temp::: '),
        ];
    }
}
