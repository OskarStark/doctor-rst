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

use App\Rule\VersionaddedDirectiveMinVersion;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class VersionaddedDirectiveMinVersionTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, string $minVersion, RstSample $sample): void
    {
        $rule = new VersionaddedDirectiveMinVersion();
        $rule->setOptions([
            'min_version' => $minVersion,
        ]);

        self::assertEquals(
            $expected,
            $rule->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    /**
     * @return \Iterator<(int|string), array{ViolationInterface, string, RstSample}>
     */
    public static function checkProvider(): iterable
    {
        yield [
            NullViolation::create(),
            '3.4',
            new RstSample('.. versionadded:: 3.4'),
        ];
        yield [
            NullViolation::create(),
            '3.4',
            new RstSample('.. versionadded:: 4.2'),
        ];
        yield [
            Violation::from(
                'Please only provide ".. versionadded::" if the version is greater/equal "3.4"',
                'filename',
                1,
                '.. versionadded:: 2.8',
            ),
            '3.4',
            new RstSample('.. versionadded:: 2.8'),
        ];
    }
}
