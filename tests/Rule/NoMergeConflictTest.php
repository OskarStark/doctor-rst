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

use App\Rule\NoMergeConflict;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class NoMergeConflictTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new NoMergeConflict())->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    public static function checkProvider(): iterable
    {
        yield 'valid' => [
            NullViolation::create(),
            new RstSample([
                '',
            ]),
        ];

        yield 'invalid in line 1' => [
            Violation::from(
                'Please get rid of the merge conflict',
                'filename',
                1,
                '<<<<<<< HEAD',
            ),
            new RstSample([
                '<<<<<<< HEAD',
                '=======',
                '>>>>>>> 1234567890',
            ]),
        ];

        yield 'invalid in line 2' => [
            Violation::from(
                'Please get rid of the merge conflict',
                'filename',
                2,
                '<<<<<<< HEAD',
            ),
            new RstSample([
                '',
                '<<<<<<< HEAD',
                '=======',
                '>>>>>>> 1234567890',
            ], 1),
        ];
    }
}
