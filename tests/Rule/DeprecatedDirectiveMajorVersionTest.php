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

use App\Rule\DeprecatedDirectiveMajorVersion;
use App\Tests\RstSample;
use Composer\Semver\VersionParser;

final class DeprecatedDirectiveMajorVersionTest extends \App\Tests\UnitTestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check(?string $expected, int $majorVersion, RstSample $sample): void
    {
        $rule = (new DeprecatedDirectiveMajorVersion(new VersionParser()));
        $rule->setOptions(['major_version' => $majorVersion]);

        static::assertSame($expected, $rule->check($sample->lines(), $sample->lineNumber()));
    }

    /**
     * @return \Generator<array{0: string|null, 1: int, 2: RstSample}>
     */
    public function checkProvider(): \Generator
    {
        yield [
            null,
            3,
            new RstSample('.. deprecated:: 3'),
        ];
        yield [
            null,
            3,
            new RstSample('.. deprecated:: 3.4'),
        ];
        yield [
            null,
            3,
            new RstSample('.. deprecated:: 3.4.0'),
        ];
        yield [
            null,
            3,
            new RstSample('.. deprecated:: 3.4.0.0'),
        ];
        yield [
            null,
            3,
            new RstSample('.. deprecated:: 3.4   '),
        ];
        yield [
            'You are not allowed to use version "2.7". Only major version "3" is allowed.',
            3,
            new RstSample('.. deprecated:: 2.7'),
        ];
        yield [
            'You are not allowed to use version "4.0". Only major version "3" is allowed.',
            3,
            new RstSample('.. deprecated:: 4.0'),
        ];
        yield [
            'Please provide a numeric version behind ".. deprecated::" instead of "foo"',
            3,
            new RstSample('.. deprecated:: foo'),
        ];
    }
}
