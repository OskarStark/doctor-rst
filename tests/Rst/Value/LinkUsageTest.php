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

namespace App\Tests\Rst\Value;

use App\Rst\Value\LinkName;
use App\Rst\Value\LinkUsage;
use App\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class LinkUsageTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('fromLineProvider')]
    public function fromLine(string $expected, string $line): void
    {
        $usage = LinkUsage::fromLine($line);

        self::assertSame($expected, $usage->name()->value());
    }

    /**
     * @return \Generator<array{0: string, 1: string}>
     */
    public static function fromLineProvider(): iterable
    {
        yield ['Link1', '`Link1`_'];
        yield ['Link 1', '`Link 1`_'];
    }

    #[Test]
    public function fromLinkName(): void
    {
        $name = 'foo';

        self::assertSame(
            $name,
            LinkUsage::fromLinkName(LinkName::fromString($name))->name()->value(),
        );
    }
}
