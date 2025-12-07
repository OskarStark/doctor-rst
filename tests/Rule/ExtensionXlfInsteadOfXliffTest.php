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

use App\Rule\ExtensionXlfInsteadOfXliff;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class ExtensionXlfInsteadOfXliffTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new ExtensionXlfInsteadOfXliff())->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    /**
     * @return array<array{0: ViolationInterface, 1: RstSample}>
     */
    public static function checkProvider(): iterable
    {
        yield from [
            [
                Violation::from(
                    'Please use ".xlf" extension instead of ".xliff"',
                    'filename',
                    1,
                    'messages.xliff',
                ),
                new RstSample('messages.xliff'),
            ],
            [
                NullViolation::create(),
                new RstSample('messages.xlf'),
            ],
        ];
    }
}
