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

namespace App\Value;

use Webmozart\Assert\Assert;

final class RuleGroup
{
    private const GROUP_EXPERIMENTAL = '@Experimental';
    private const GROUP_SONATA = '@Sonata';
    private const GROUP_SYMFONY = '@Symfony';
    private const ALLOWED_GROUPS = [
        self::GROUP_EXPERIMENTAL,
        self::GROUP_SONATA,
        self::GROUP_SYMFONY,
    ];
    private readonly string $name;

    private function __construct(string $name)
    {
        $name = trim($name);

        Assert::stringNotEmpty($name);
        Assert::notWhitespaceOnly($name);
        Assert::oneOf($name, self::ALLOWED_GROUPS);

        $this->name = $name;
    }

    public static function fromString(string $name): self
    {
        return new self($name);
    }

    public static function Experimental(): self
    {
        return new self(self::GROUP_EXPERIMENTAL);
    }

    public static function Sonata(): self
    {
        return new self(self::GROUP_SONATA);
    }

    public static function Symfony(): self
    {
        return new self(self::GROUP_SYMFONY);
    }

    public function equals(self $other): bool
    {
        return $other->name() === $this->name;
    }

    public function name(): string
    {
        return $this->name;
    }
}
