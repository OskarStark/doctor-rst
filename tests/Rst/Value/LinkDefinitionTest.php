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

use App\Rst\Value\LinkDefinition;
use App\Rst\Value\LinkName;
use App\Rst\Value\LinkUrl;
use App\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class LinkDefinitionTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('fromLineProvider')]
    public function fromLine(string $expectedName, string $expectedUrl, string $line): void
    {
        $definition = LinkDefinition::fromLine($line);

        self::assertSame($expectedName, $definition->name()->value());
        self::assertSame($expectedUrl, $definition->url()->value());
    }

    /**
     * @return \Generator<array{0: string, 1: string, 2: string}>
     */
    public static function fromLineProvider(): iterable
    {
        yield ['Link1', 'https://example.com', '.. _Link1: https://example.com'];
        yield ['Link 1', 'https://example.com', '.. _`Link 1`: https://example.com'];
        yield ['Link1', 'https://example.com', '   .. _Link1: https://example.com'];
        yield ['Link 1', 'https://example.com', '   .. _`Link 1`: https://example.com'];
        yield ['Link1', 'https://example.com', '    .. _Link1: https://example.com'];
    }

    #[Test]
    public function fromValues(): void
    {
        $name = LinkName::fromString('foo');
        $url = LinkUrl::fromString('https://example.com');

        $definition = LinkDefinition::fromValues($name, $url);

        self::assertSame('foo', $definition->name()->value());
        self::assertSame('https://example.com', $definition->url()->value());
    }
}
