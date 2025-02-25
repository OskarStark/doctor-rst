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

use App\Rule\SpaceBetweenLabelAndLinkInRef;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class SpaceBetweenLabelAndLinkInRefTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new SpaceBetweenLabelAndLinkInRef())->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    /**
     * @return \Generator<array{0: ViolationInterface, 1: RstSample}>
     */
    public static function checkProvider(): iterable
    {
        yield [
            Violation::from(
                'Please add a space between "receiving them via a worker" and "<messenger-worker>" inside :ref: directive',
                'filename',
                1,
                ':ref:`receiving them via a worker<messenger-worker>`',
            ),
            new RstSample(':ref:`receiving them via a worker<messenger-worker>`'),
        ];

        yield [
            NullViolation::create(),
            new RstSample(':ref:`receiving them via a worker <messenger-worker>`'),
        ];
    }
}
