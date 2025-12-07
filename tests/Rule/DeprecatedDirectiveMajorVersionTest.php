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

use App\Rule\DeprecatedDirectiveMajorVersion;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use Composer\Semver\VersionParser;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class DeprecatedDirectiveMajorVersionTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, int $majorVersion, RstSample $sample): void
    {
        $rule = (new DeprecatedDirectiveMajorVersion(new VersionParser()));
        $rule->setOptions(['major_version' => $majorVersion]);

        self::assertEquals($expected, $rule->check($sample->lines, $sample->lineNumber, 'filename'));
    }

    /**
     * @return \Generator<array{0: ViolationInterface, 1: int, 2: RstSample}>
     */
    public static function checkProvider(): iterable
    {
        yield [
            NullViolation::create(),
            3,
            new RstSample('.. deprecated:: 3'),
        ];
        yield [
            NullViolation::create(),
            3,
            new RstSample('.. deprecated:: 3.4'),
        ];
        yield [
            NullViolation::create(),
            3,
            new RstSample('.. deprecated:: 3.4.0'),
        ];
        yield [
            NullViolation::create(),
            3,
            new RstSample('.. deprecated:: 3.4.0.0'),
        ];
        yield [
            NullViolation::create(),
            3,
            new RstSample('.. deprecated:: 3.4   '),
        ];
        yield [
            Violation::from(
                'You are not allowed to use version "2.7". Only major version "3" is allowed.',
                'filename',
                1,
                '.. deprecated:: 2.7',
            ),
            3,
            new RstSample('.. deprecated:: 2.7'),
        ];
        yield [
            Violation::from(
                'You are not allowed to use version "4.0". Only major version "3" is allowed.',
                'filename',
                1,
                '.. deprecated:: 4.0',
            ),
            3,
            new RstSample('.. deprecated:: 4.0'),
        ];
        yield [
            Violation::from(
                'Please provide a numeric version behind ".. deprecated::" instead of "foo"',
                'filename',
                1,
                '.. deprecated:: foo',
            ),
            3,
            new RstSample('.. deprecated:: foo'),
        ];
    }
}
