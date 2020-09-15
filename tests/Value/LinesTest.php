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

namespace App\Tests\Value;

use App\Value\Lines;
use PHPUnit\Framework\TestCase;

final class LinesTest extends TestCase
{
    public function testCurrentThrowsOutOfBoundsExceptionWhenLinesIsInvalid(): void
    {
        $lines = Lines::fromArray([]);

        self::assertFalse($lines->valid());

        $this->expectException(\OutOfBoundsException::class);
        $this->expectExceptionMessage('Line "0" does not exists.');

        $lines->current();
    }

    public function testKeyThrowsOutOfBoundsExceptionWhenLinesIsInvalid(): void
    {
        $lines = Lines::fromArray([]);

        self::assertFalse($lines->valid());

        $this->expectException(\OutOfBoundsException::class);
        $this->expectExceptionMessage('Line "0" does not exists.');

        $lines->key();
    }

    public function testSeekRestoresCurrentPositionWhenTheGivenPositionIsInvalid(): void
    {
        $lines = Lines::fromArray([
            "hello\n",
            "world\n",
        ]);

        $lines->seek(1);

        $exception = null;
        try {
            $lines->seek(54);
        } catch (\OutOfBoundsException $exception) {
            self::assertSame('Line "54" does not exists.', $exception->getMessage());
        }

        self::assertNotNull($exception, sprintf('Expected "%s" exception to be thrown.', \OutOfBoundsException::class));
        self::assertSame(1, $lines->key());
    }
}
