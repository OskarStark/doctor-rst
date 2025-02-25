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

use App\Rule\DeprecatedDirectiveMinVersion;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class DeprecatedDirectiveMinVersionTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, string $minVersion, RstSample $sample): void
    {
        $rule = (new DeprecatedDirectiveMinVersion());
        $rule->setOptions(['min_version' => $minVersion]);

        self::assertEquals($expected, $rule->check($sample->lines, $sample->lineNumber, 'filename'));
    }

    /**
     * @return \Generator<array{0: ViolationInterface, 1: string, 2: RstSample}>
     */
    public static function checkProvider(): iterable
    {
        yield [
            NullViolation::create(),
            '3.4',
            new RstSample('.. deprecated:: 3.4'),
        ];
        yield [
            NullViolation::create(),
            '3.4',
            new RstSample('.. deprecated:: 4.2'),
        ];
        yield [
            Violation::from(
                'Please only provide ".. deprecated::" if the version is greater/equal "3.4"',
                'filename',
                1,
                '.. deprecated:: 2.8',
            ),
            '3.4',
            new RstSample('.. deprecated:: 2.8'),
        ];
    }
}
