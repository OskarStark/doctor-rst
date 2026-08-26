<?php

declare(strict_types=1);

/**
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
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use Composer\Semver\VersionParser;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class VersionaddedDirectiveMajorVersionTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, int $majorVersion, RstSample $sample): void
    {
        $rule = new VersionaddedDirectiveMajorVersion(new VersionParser());
        $rule->setOptions([
            'major_version' => $majorVersion,
        ]);

        self::assertEquals(
            $expected,
            $rule->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    /**
     * @return \Iterator<(int|string), array{ViolationInterface, int, RstSample}>
     */
    public static function checkProvider(): iterable
    {
        yield [
            NullViolation::create(),
            3,
            new RstSample('.. versionadded:: 3'),
        ];
        yield [
            NullViolation::create(),
            3,
            new RstSample('.. versionadded:: 3.4'),
        ];
        yield [
            NullViolation::create(),
            3,
            new RstSample('.. versionadded:: 3.4.0'),
        ];
        yield [
            NullViolation::create(),
            3,
            new RstSample('.. versionadded:: 3.4.0.0'),
        ];
        yield [
            NullViolation::create(),
            3,
            new RstSample('.. versionadded:: 3.4   '),
        ];
        yield [
            Violation::from(
                'You are not allowed to use version "2.7". Only major version "3" is allowed.',
                'filename',
                1,
                '.. versionadded:: 2.7',
            ),
            3,
            new RstSample('.. versionadded:: 2.7'),
        ];
        yield [
            Violation::from(
                'You are not allowed to use version "4.0". Only major version "3" is allowed.',
                'filename',
                1,
                '.. versionadded:: 4.0',
            ),
            3,
            new RstSample('.. versionadded:: 4.0'),
        ];
        yield [
            Violation::from(
                'Please provide a numeric version behind ".. versionadded::" instead of "foo"',
                'filename',
                1,
                '.. versionadded:: foo',
            ),
            3,
            new RstSample('.. versionadded:: foo'),
        ];
    }
}
