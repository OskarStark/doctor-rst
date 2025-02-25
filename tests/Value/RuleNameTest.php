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

namespace App\Tests\Value;

use App\Tests\UnitTestCase;
use App\Value\RuleName;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;

final class RuleNameTest extends UnitTestCase
{
    #[Test]
    public function fromStringTrimsValue(): void
    {
        $value = self::faker()->word();
        $untrimmed = ' '.$value.' ';

        self::assertSame(
            $value,
            RuleName::fromString($untrimmed)->toString(),
        );
    }

    #[Test]
    public function fromString(): void
    {
        $value = self::faker()->word();

        self::assertSame(
            $value,
            RuleName::fromString($value)->toString(),
        );
    }

    #[Test]
    public function fromClassStringTrimsValue(): void
    {
        self::assertSame(
            'baz_rule',
            RuleName::fromClassString(' Foo\Bar\BazRule ')->toString(),
        );
    }

    #[Test]
    public function fromClassString(): void
    {
        self::assertSame(
            'baz_rule',
            RuleName::fromClassString('Foo\Bar\BazRule')->toString(),
        );
    }

    #[Test]
    #[DataProviderExternal(\Ergebnis\DataProvider\StringProvider::class, 'blank')]
    #[DataProviderExternal(\Ergebnis\DataProvider\StringProvider::class, 'empty')]
    public function fromStringThrowsException(string $value): void
    {
        $this->expectException(\InvalidArgumentException::class);

        RuleName::fromString($value);
    }

    #[Test]
    #[DataProviderExternal(\Ergebnis\DataProvider\StringProvider::class, 'blank')]
    #[DataProviderExternal(\Ergebnis\DataProvider\StringProvider::class, 'empty')]
    public function fromClassStringThrowsException(string $value): void
    {
        $this->expectException(\InvalidArgumentException::class);

        RuleName::fromClassString($value);
    }
}
