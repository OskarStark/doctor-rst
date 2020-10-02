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

use App\Rule\DeprecatedDirectiveMinVersion;
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;

class DeprecatedDirectiveMinVersionTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check(?string $expected, string $minVersion, RstSample $sample)
    {
        $rule = (new DeprecatedDirectiveMinVersion());
        $rule->setOptions(['min_version' => $minVersion]);

        static::assertSame($expected, $rule->check($sample->lines(), $sample->lineNumber()));
    }

    /**
     * @return \Generator<array{0: string|null, 1: string, 2: RstSample}>
     */
    public function checkProvider(): \Generator
    {
        yield [
            null,
            '3.4',
            new RstSample('.. deprecated:: 3.4'),
        ];
        yield[
            null,
            '3.4',
            new RstSample('.. deprecated:: 4.2'),
        ];
        yield[
            'Please only provide ".. deprecated::" if the version is greater/equal "3.4"',
            '3.4',
            new RstSample('.. deprecated:: 2.8'),
        ];
    }
}
