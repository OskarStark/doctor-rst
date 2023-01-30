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

use App\Rule\VersionaddedDirectiveMajorVersion;
use App\Tests\RstSample;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use Composer\Semver\VersionParser;

final class VersionaddedDirectiveMajorVersionTest extends \App\Tests\UnitTestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check(ViolationInterface $expected, int $majorVersion, RstSample $sample): void
    {
        $rule = new VersionaddedDirectiveMajorVersion(new VersionParser());
        $rule->setOptions([
            'major_version' => $majorVersion,
        ]);

        static::assertEquals(
            $expected,
            $rule->check($sample->lines(), $sample->lineNumber(), 'filename')
        );
    }

    /**
     * @return array<array{0: ViolationInterface, 1: int, 2: RstSample}>
     */
    public function checkProvider(): array
    {
        return [
            [
                NullViolation::create(),
                3,
                new RstSample('.. versionadded:: 3'),
            ],
            [
                NullViolation::create(),
                3,
                new RstSample('.. versionadded:: 3.4'),
            ],
            [
                NullViolation::create(),
                3,
                new RstSample('.. versionadded:: 3.4.0'),
            ],
            [
                NullViolation::create(),
                3,
                new RstSample('.. versionadded:: 3.4.0.0'),
            ],
            [
                NullViolation::create(),
                3,
                new RstSample('.. versionadded:: 3.4   '),
            ],
            [
                Violation::from(
                    'You are not allowed to use version "2.7". Only major version "3" is allowed.',
                    'filename',
                    1,
                    ''
                ),
                3,
                new RstSample('.. versionadded:: 2.7'),
            ],
            [
                Violation::from(
                    'You are not allowed to use version "4.0". Only major version "3" is allowed.',
                    'filename',
                    1,
                    ''
                ),
                3,
                new RstSample('.. versionadded:: 4.0'),
            ],
            [
                Violation::from(
                    'Please provide a numeric version behind ".. versionadded::" instead of "foo"',
                    'filename',
                    1,
                    ''
                ),
                3,
                new RstSample('.. versionadded:: foo'),
            ],
        ];
    }
}
