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

use App\Rule\DeprecatedDirectiveShouldHaveVersion;
use App\Tests\RstSample;
use Composer\Semver\VersionParser;

final class DeprecatedDirectiveShouldHaveVersionTest extends \App\Tests\UnitTestCase
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
            (new DeprecatedDirectiveShouldHaveVersion(new VersionParser()))
                ->check($sample->lines(), $sample->lineNumber())
        );
    }

    /**
     * @return \Generator<array{0: string|null, 1: RstSample}>
     */
    public function checkProvider(): \Generator
    {
        yield [
            null,
            new RstSample('.. deprecated:: 1'),
        ];
        yield [
            null,
            new RstSample('.. deprecated:: 1.2'),
        ];
        yield [
            null,
            new RstSample('.. deprecated:: 1.2.0'),
        ];
        yield [
            null,
            new RstSample('.. deprecated:: 1.2   '),
        ];
        yield [
            'Please provide a version behind ".. deprecated::"',
            new RstSample('.. deprecated::'),
        ];
        yield [
            'Please provide a numeric version behind ".. deprecated::" instead of "foo"',
            new RstSample('.. deprecated:: foo'),
        ];
    }
}
