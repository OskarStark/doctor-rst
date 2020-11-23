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

use App\Rule\YarnDevOptionAtTheEnd;
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;

final class YarnDevOptionAtTheEndTest extends TestCase
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
            (new YarnDevOptionAtTheEnd())->check($sample->lines(), $sample->lineNumber())
        );
    }

    /**
     * @return \Generator<array{0: string|null, 1: RstSample}>
     */
    public function checkProvider(): \Generator
    {
        yield [
            'Please move "--dev" option to the end of the command',
            new RstSample('yarn add --dev jquery'),
        ];

        yield [
            null,
            new RstSample('yarn add jquery --dev'),
        ];
    }
}
