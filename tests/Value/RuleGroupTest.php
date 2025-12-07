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
use App\Value\RuleGroup;
use Ergebnis\DataProvider\StringProvider;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;

final class RuleGroupTest extends UnitTestCase
{
    #[Test]
    public function fromStringThrowsExceptionIfUnknownGroup(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        RuleGroup::fromString(self::faker()->word);
    }

    #[Test]
    #[DataProviderExternal(StringProvider::class, 'blank')]
    #[DataProviderExternal(StringProvider::class, 'empty')]
    public function fromStringThrowsException(string $value): void
    {
        $this->expectException(\InvalidArgumentException::class);

        RuleGroup::fromString($value);
    }

    #[Test]
    #[DataProvider('definedProvider')]
    public function defined(string $expected, RuleGroup $group): void
    {
        self::assertSame(
            $expected,
            $group->name(),
        );
    }

    /**
     * @return \Generator<string, array{0: string, 1: RuleGroup}>
     */
    public static function definedProvider(): iterable
    {
        yield '@Experimental' => [
            '@Experimental',
            RuleGroup::Experimental(),
        ];

        yield '@Symfony' => [
            '@Symfony',
            RuleGroup::Symfony(),
        ];

        yield '@Sonata' => [
            '@Sonata',
            RuleGroup::Sonata(),
        ];
    }

    #[Test]
    #[DataProvider('equalsProvider')]
    public function equals(bool $expected, RuleGroup $group, RuleGroup $other): void
    {
        self::assertSame(
            $expected,
            $group->equals($other),
        );
    }

    /**
     * @return \Generator<array{0: bool, 1: RuleGroup, 2: RuleGroup}>
     */
    public static function equalsProvider(): iterable
    {
        yield [
            true,
            RuleGroup::Experimental(),
            RuleGroup::Experimental(),
        ];

        yield [
            true,
            RuleGroup::Symfony(),
            RuleGroup::fromString('@Symfony'),
        ];

        yield [
            false,
            RuleGroup::Sonata(),
            RuleGroup::Symfony(),
        ];
    }
}
