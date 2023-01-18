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

use App\Rule\LineLength;
use App\Tests\RstSample;

final class LineLengthTest extends \App\Tests\UnitTestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check(?string $expected, int $max, RstSample $sample): void
    {
        $rule = (new LineLength());
        $rule->setOptions(['max' => $max]);

        static::assertSame($expected, $rule->check($sample->lines(), $sample->lineNumber()));
    }

    public function checkProvider(): array
    {
        return [
            [
                'Line is to long (max 20) currently: 23',
                20,
                new RstSample('This is a cool sentence'),
            ],
            [
                null,
                20,
                new RstSample('This is a sentence'),
            ],
        ];
    }
}
