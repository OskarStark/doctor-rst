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

use App\Value\RuleName;
use Ergebnis\Test\Util\Helper;
use PHPUnit\Framework\TestCase;

class RuleNameTest extends TestCase
{
    use Helper;

    /**
     * @test
     */
    public function fromStringTrimsValue(): void
    {
        $value = self::faker()->word;
        $untrimmed = ' '.$value.' ';

        static::assertSame(
            $value,
            RuleName::fromString($untrimmed)->toString()
        );
    }

    /**
     * @test
     */
    public function fromString(): void
    {
        $value = self::faker()->word;

        static::assertSame(
            $value,
            RuleName::fromString($value)->toString()
        );
    }

    /**
     * @test
     */
    public function fromClassStringTrimsValue(): void
    {
        static::assertSame(
            'baz_rule',
            RuleName::fromClassString(' Foo\Bar\BazRule ')->toString()
        );
    }

    /**
     * @test
     */
    public function fromClassString(): void
    {
        static::assertSame(
            'baz_rule',
            RuleName::fromClassString('Foo\Bar\BazRule')->toString()
        );
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

        RuleName::fromString($value);
    }

    /**
     * @test
     *
     * @dataProvider \Ergebnis\Test\Util\DataProvider\StringProvider::empty()
     * @dataProvider \Ergebnis\Test\Util\DataProvider\StringProvider::blank()
     */
    public function fromClassStringThrowsException(string $value): void
    {
        $this->expectException(\InvalidArgumentException::class);

        RuleName::fromClassString($value);
    }
}
