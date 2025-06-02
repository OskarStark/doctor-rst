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

use App\Rule\EnsureCodeBlockIndentation;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class EnsureCodeBlockIndentationTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new EnsureCodeBlockIndentation())->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    /**
     * @return \Generator<array{0: ViolationInterface, 1: RstSample}>
     */
    public static function checkProvider(): iterable
    {
        yield [
            NullViolation::create(),
            new RstSample([
                '',
                '.. code-block:: yml',
            ], 1),
        ];

        yield [
            Violation::from(
                'Please indent code block with multiple of 4 spaces, or zero. Actually 1 space(s)',
                'filename',
                2,
                '.. code-block:: yml',
            ),
            new RstSample([
                '',
                ' .. code-block:: yml',
            ], 1),
        ];

        yield [
            NullViolation::create(),
            new RstSample([
                '',
                '    .. code-block:: yml',
            ], 1),
        ];

        yield [
            Violation::from(
                'Please indent code block with multiple of 4 spaces, or zero. Actually 5 space(s)',
                'filename',
                2,
                '.. code-block:: yml',
            ),
            new RstSample([
                '',
                '     .. code-block:: yml',
            ], 1),
        ];
    }
}
