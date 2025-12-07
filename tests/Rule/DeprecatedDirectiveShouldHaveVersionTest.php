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

use App\Rule\DeprecatedDirectiveShouldHaveVersion;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use Composer\Semver\VersionParser;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class DeprecatedDirectiveShouldHaveVersionTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new DeprecatedDirectiveShouldHaveVersion(new VersionParser()))
                ->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    /**
     * @return \Generator<array{0: ViolationInterface, 1: RstSample}>
     */
    public static function checkProvider(): iterable
    {
        yield [
            NullViolation::create(),
            new RstSample('.. deprecated:: 1'),
        ];
        yield [
            NullViolation::create(),
            new RstSample('.. deprecated:: 1.2'),
        ];
        yield [
            NullViolation::create(),
            new RstSample('.. deprecated:: 1.2.0'),
        ];
        yield [
            NullViolation::create(),
            new RstSample('.. deprecated:: 1.2   '),
        ];
        yield [
            Violation::from(
                'Please provide a version behind ".. deprecated::"',
                'filename',
                1,
                '.. deprecated::',
            ),
            new RstSample('.. deprecated::'),
        ];
        yield [
            Violation::from(
                'Please provide a numeric version behind ".. deprecated::" instead of "foo"',
                'filename',
                1,
                '.. deprecated:: foo',
            ),
            new RstSample('.. deprecated:: foo'),
        ];
    }
}
