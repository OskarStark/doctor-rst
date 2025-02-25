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

use App\Rule\VersionaddedDirectiveShouldHaveVersion;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use Composer\Semver\VersionParser;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class VersionaddedDirectiveShouldHaveVersionTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new VersionaddedDirectiveShouldHaveVersion(new VersionParser()))
                ->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    /**
     * @return array<array{0: ViolationInterface, 1: RstSample}>
     */
    public static function checkProvider(): iterable
    {
        yield from [
            [
                NullViolation::create(),
                new RstSample('.. versionadded:: 1'),
            ],
            [
                NullViolation::create(),
                new RstSample('.. versionadded:: 1.2'),
            ],
            [
                NullViolation::create(),
                new RstSample('.. versionadded:: 1.2.0'),
            ],
            [
                NullViolation::create(),
                new RstSample('.. versionadded:: 1.2   '),
            ],
            [
                Violation::from(
                    'Please provide a version behind ".. versionadded::"',
                    'filename',
                    1,
                    '.. versionadded::',
                ),
                new RstSample('.. versionadded::'),
            ],
            [
                Violation::from(
                    'Please provide a numeric version behind ".. versionadded::" instead of "foo"',
                    'filename',
                    1,
                    '.. versionadded:: foo',
                ),
                new RstSample('.. versionadded:: foo'),
            ],
        ];
    }
}
