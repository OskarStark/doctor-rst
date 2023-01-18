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

final class MaxColonsTest extends \App\Tests\UnitTestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check(?string $expected, RstSample $sample): void
    {
        static::assertSame(
            $expected,
            (new MaxColons())->check($sample->lines(), $sample->lineNumber())
        );
    }

    /**
     * @return \Generator<array{0: string|null, 1: RstSample}>
     */
    public function checkProvider(): \Generator
    {
        yield [
            null,
            new RstSample('temp'),
        ];

        yield [
            null,
            new RstSample('temp:'),
        ];

        yield [
            null,
            new RstSample('temp::'),
        ];

        yield [
            'Please use max 2 colons at the end.',
            new RstSample('temp:::'),
        ];

        yield [
            'Please use max 2 colons at the end.',
            new RstSample(' temp:::'),
        ];

        yield [
            'Please use max 2 colons at the end.',
            new RstSample(' temp::: '),
        ];
    }
}
