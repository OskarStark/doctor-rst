<?php

declare(strict_types=1);

/*
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
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;

final class ExtensionXlfInsteadOfXliffTest extends \App\Tests\UnitTestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        static::assertEquals(
            $expected,
            (new ExtensionXlfInsteadOfXliff())->check($sample->lines(), $sample->lineNumber(), 'filename')
        );
    }

    /**
     * @return array<array{0: ViolationInterface, 1: RstSample}>
     */
    public function checkProvider(): array
    {
        return [
            [
                Violation::from(
                    'Please use ".xlf" extension instead of ".xliff"',
                    'filename',
                    1,
                    ''
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
