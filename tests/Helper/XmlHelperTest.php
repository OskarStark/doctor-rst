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

namespace App\Tests\Helper;

use App\Helper\XmlHelper;
use App\Tests\UnitTestCase;
use App\Value\Line;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class XmlHelperTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('isCommentProvider')]
    public function isComment(bool $expected, string $line, ?bool $closed): void
    {
        self::assertSame(
            $expected,
            XmlHelper::isComment(new Line($line), $closed),
        );
    }

    /**
     * @return \Generator<array{0: bool, 1: string, 2: null|bool}>
     */
    public static function isCommentProvider(): iterable
    {
        yield [true, '<!--', null];
        yield [true, '-->', null];

        yield [true, '<!-- comment -->', true];
        yield [false, '<!-- comment -->', false];
        yield [true, '<!-- comment', false];
        yield [false, '<!-- comment', true];

        yield [false, 'no comment', null];
        yield [false, 'no comment', true];
        yield [false, 'no comment', false];
    }
}
