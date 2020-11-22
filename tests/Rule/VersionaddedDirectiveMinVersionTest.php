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

use App\Rule\VersionaddedDirectiveMinVersion;
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;

final class VersionaddedDirectiveMinVersionTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check(?string $expected, string $minVersion, RstSample $sample): void
    {
        $rule = new VersionaddedDirectiveMinVersion();
        $rule->setOptions([
            'min_version' => $minVersion,
        ]);

        static::assertSame(
            $expected,
            $rule->check($sample->lines(), $sample->lineNumber())
        );
    }

    /**
     * @return array<array{0: string|null, 1: string, 2: RstSample}>
     */
    public function checkProvider(): array
    {
        return [
            [
                null,
                '3.4',
                new RstSample('.. versionadded:: 3.4'),
            ],
            [
                null,
                '3.4',
                new RstSample('.. versionadded:: 4.2'),
            ],
            [
                'Please only provide ".. versionadded::" if the version is greater/equal "3.4"',
                '3.4',
                new RstSample('.. versionadded:: 2.8'),
            ],
        ];
    }
}
