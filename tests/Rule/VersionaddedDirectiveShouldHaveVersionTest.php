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

use App\Rule\VersionaddedDirectiveShouldHaveVersion;
use App\Tests\RstSample;
use Composer\Semver\VersionParser;

final class VersionaddedDirectiveShouldHaveVersionTest extends \App\Tests\UnitTestCase
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
            (new VersionaddedDirectiveShouldHaveVersion(new VersionParser()))
                ->check($sample->lines(), $sample->lineNumber())
        );
    }

    /**
     * @return array<array{0: string|null, 1: RstSample}>
     */
    public function checkProvider(): array
    {
        return [
            [
                null,
                new RstSample('.. versionadded:: 1'),
            ],
            [
                null,
                new RstSample('.. versionadded:: 1.2'),
            ],
            [
                null,
                new RstSample('.. versionadded:: 1.2.0'),
            ],
            [
                null,
                new RstSample('.. versionadded:: 1.2   '),
            ],
            [
                'Please provide a version behind ".. versionadded::"',
                new RstSample('.. versionadded::'),
            ],
            [
                'Please provide a numeric version behind ".. versionadded::" instead of "foo"',
                new RstSample('.. versionadded:: foo'),
            ],
        ];
    }
}
