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

use App\Rule\NoTypographicQuotes;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class NoTypographicQuotesTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new NoTypographicQuotes())->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    public static function checkProvider(): iterable
    {
        yield from [
            [
                Violation::from(
                    'Please use straight single quotes (\' ... \') instead of curly quotes (‘ ... ’)',
                    'filename',
                    1,
                    'Lorem ipsum ‘dolor sit amet’, consectetur adipiscing elit.',
                ),
                new RstSample('Lorem ipsum ‘dolor sit amet’, consectetur adipiscing elit.'),
            ],
            [
                Violation::from(
                    'Please use straight double quotes (" ... ") instead of curly quotes (“ ... ”)',
                    'filename',
                    1,
                    'Lorem ipsum “dolor sit amet”, consectetur adipiscing elit.',
                ),
                new RstSample('Lorem ipsum “dolor sit amet”, consectetur adipiscing elit.'),
            ],
            [
                Violation::from(
                    'Please use straight double quotes (" ... ") instead of curly quotes (“ ... ”)',
                    'filename',
                    1,
                    'Lorem ipsum „dolor sit amet“, consectetur adipiscing elit.',
                ),
                new RstSample('Lorem ipsum „dolor sit amet“, consectetur adipiscing elit.'),
            ],
            [
                Violation::from(
                    'Please use straight double quotes (" ... ") instead of curly quotes (“ ... ”)',
                    'filename',
                    1,
                    'Lorem ipsum «dolor sit amet», consectetur adipiscing elit.',
                ),
                new RstSample('Lorem ipsum «dolor sit amet», consectetur adipiscing elit.'),
            ],
        ];
    }
}
