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

namespace App\Rst\Value;

use Webmozart\Assert\Assert;

final class LinkName
{
    /** @var string */
    private $value;

    private function __construct(string $value)
    {
        $this->value = trim($value);
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function toLower(): string
    {
        return strtolower($this->value);
    }
}
