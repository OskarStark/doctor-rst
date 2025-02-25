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

use App\Rule\NoBrokenRefDirective;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class NoBrokenRefDirectiveTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new NoBrokenRefDirective())->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    public static function checkProvider(): iterable
    {
        yield from [
            [
                Violation::from(
                    'Please use correct syntax for :ref: directive',
                    'filename',
                    1,
                    'see ref:`Redis section <messenger-redis-transport>` below',
                ),
                new RstSample('see ref:`Redis section <messenger-redis-transport>` below'),
            ],
            [
                Violation::from(
                    'Please use correct syntax for :ref: directive',
                    'filename',
                    1,
                    'see :ref `Redis section <messenger-redis-transport>` below',
                ),
                new RstSample('see :ref `Redis section <messenger-redis-transport>` below'),
            ],
            [
                NullViolation::create(),
                new RstSample('see :ref:`Redis section <messenger-redis-transport>` below'),
            ],
            [
                NullViolation::create(),
                new RstSample('If you prefer to use'),
            ],
            [
                NullViolation::create(),
                new RstSample('Then use the :method:`Symfony\\Component\\Lock\\LockInterface::refresh` method'),
            ],
            [
                NullViolation::create(),
                new RstSample('new Link(href: style.css'),
            ],
        ];
    }
}
