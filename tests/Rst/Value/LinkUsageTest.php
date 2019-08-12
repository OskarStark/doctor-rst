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

namespace App\Tests\Rst\Value;

use App\Rst\Value\LinkName;
use App\Rst\Value\LinkUsage;
use PHPUnit\Framework\TestCase;

final class LinkUsageTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider fromLineProvider
     */
    public function fromLine(string $expected, string $line)
    {
        $usage = LinkUsage::fromLine($line);

        $this->assertSame($expected, $usage->name()->value());
    }

    public function fromLineProvider(): \Generator
    {
        yield ['Link1', '`Link1`_'];
        yield ['Link 1', '`Link 1`_'];
    }

    /**
     * @test
     */
    public function fromLinkName()
    {
        $name = 'foo';

        $this->assertSame(
            $name,
            (LinkUsage::fromLinkName(LinkName::fromString($name)))->name()->value()
        );
    }
}
