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

use App\Rule\SpaceBetweenLabelAndLinkInRef;
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;

final class SpaceBetweenLabelAndLinkInRefTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check(?string $expected, RstSample $sample)
    {
        static::assertSame(
            $expected,
            (new SpaceBetweenLabelAndLinkInRef())->check($sample->lines(), $sample->lineNumber())
        );
    }

    /**
     * @return \Generator<array{0: string|null, 1: RstSample}>
     */
    public function checkProvider(): \Generator
    {
        yield [
            'Please add a space between "receiving them via a worker" and "<messenger-worker>" inside :ref: directive',
            new RstSample(':ref:`receiving them via a worker<messenger-worker>`'),
        ];

        yield [
            null,
            new RstSample(':ref:`receiving them via a worker <messenger-worker>`'),
        ];
    }
}
