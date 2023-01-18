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

use App\Value\RuleGroup;
use Ergebnis\Test\Util\Helper;

final class RuleGroupTest extends \App\Tests\UnitTestCase
{
    use Helper;

    /**
     * @test
     */
    public function fromStringThrowsExceptionIfUnknownGroup(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        RuleGroup::fromString(self::faker()->word);
    }

    /**
     * @test
     *
     * @dataProvider \Ergebnis\Test\Util\DataProvider\StringProvider::empty()
     * @dataProvider \Ergebnis\Test\Util\DataProvider\StringProvider::blank()
     */
    public function fromStringThrowsException(string $value): void
    {
        $this->expectException(\InvalidArgumentException::class);

        RuleGroup::fromString($value);
    }

    /**
     * @test
     *
     * @dataProvider definedProvider
     */
    public function defined(string $expected, RuleGroup $group): void
    {
        static::assertSame(
            $expected,
            $group->name()
        );
    }

    /**
     * @return \Generator<string, array{0: string, 1: RuleGroup}>
     */
    public function definedProvider(): \Generator
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

    /**
     * @test
     *
     * @dataProvider equalsProvider
     */
    public function equals(bool $expected, RuleGroup $group, RuleGroup $other): void
    {
        static::assertSame(
            $expected,
            $group->equals($other)
        );
    }

    /**
     * @return \Generator<array{0: bool, 1: RuleGroup, 2: RuleGroup}>
     */
    public function equalsProvider(): \Generator
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
