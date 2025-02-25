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

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use App\Rule\EnsureLinkBottom;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;

final class EnsureLinkBottomTest extends UnitTestCase
{
    #[DataProvider('checkProvider')]
    #[Test]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new EnsureLinkBottom())->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    /**
     * @return \Generator<array{0: ViolationInterface, 1: RstSample}>
     */
    public static function checkProvider(): iterable
    {
        yield [
            NullViolation::create(),
            new RstSample('temp'),
        ];

        yield [
            NullViolation::create(),
            new RstSample([
                '',
                '.. _`first-link`: https://foo.bar',
            ], 1),
        ];

        yield [
            NullViolation::create(),
            new RstSample([
                '',
                '.. _`first-link`: https://foo.bar',
                '.. _`second-link`: https://foo.baz',
            ], 1),
        ];

        yield [
            Violation::from(
                'Please move link definition to the bottom of the page',
                'filename',
                2,
                '.. _`first-link`: https://foo.bar',
            ),
            new RstSample([
                '',
                '.. _`first-link`: https://foo.bar',
                'text after link',
            ], 1),
        ];

        yield [
            Violation::from(
                'Please move link definition to the bottom of the page',
                'filename',
                2,
                '.. _`first-link`: https://foo.bar',
            ),
            new RstSample([
                '',
                '.. _`first-link`: https://foo.bar',
                '',
                'text after link',
            ], 1),
        ];
    }
}
