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

use App\Helper\YamlHelper;
use App\Tests\UnitTestCase;
use App\Value\Line;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class YamlHelperTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('isCommentProvider')]
    public function isComment(bool $expected, string $line): void
    {
        self::assertSame(
            $expected,
            YamlHelper::isComment(new Line($line)),
        );
    }

    /**
     * @return \Generator<array{0: bool, 1: string}>
     */
    public static function isCommentProvider(): iterable
    {
        yield [true, '# comment'];
        yield [false, 'no comment'];
    }
}
