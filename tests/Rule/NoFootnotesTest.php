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

use App\Rule\NoFootnotes;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;

final class NoFootnotesTest extends UnitTestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new NoFootnotes())->check($sample->lines(), $sample->lineNumber(), 'filename'),
        );
    }

    public static function checkProvider(): iterable
    {
        yield from [
            [
                Violation::from(
                    "Please don't use footnotes as they are not supported",
                    'filename',
                    1,
                    '.. [5] A numerical footnote. Note',
                ),
                new RstSample('.. [5] A numerical footnote. Note'),
            ],
            [
                NullViolation::create(),
                new RstSample('.. _`Symfony`: https://symfony.com'),
            ],
        ];
    }
}
