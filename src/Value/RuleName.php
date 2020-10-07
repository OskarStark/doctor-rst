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

namespace App\Value;

use function Symfony\Component\String\u;
use Webmozart\Assert\Assert;

final class RuleName
{
    private string $name;

    private function __construct(string $value)
    {
        $value = trim($value);

        Assert::stringNotEmpty($value);
        Assert::notWhitespaceOnly($value);

        $this->name = $value;
    }

    public static function fromClassString(string $class): self
    {
        $class = trim($class);

        Assert::stringNotEmpty($class);
        Assert::notWhitespaceOnly($class);

        return self::fromString(
            u(substr((string) strrchr($class, '\\'), 1))->snake()->toString()
        );
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function toString(): string
    {
        return $this->name;
    }
}
