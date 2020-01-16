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

use Webmozart\Assert\Assert;

final class RuleName
{
    /**
     * @var string
     */
    private $name;

    private function __construct(string $name)
    {
        $name = trim($name);

        Assert::stringNotEmpty($name);
        Assert::notWhitespaceOnly($name);

        $this->name = $name;
    }

    public static function fromString(string $name): self
    {
        return new self($name);
    }

    public function asString(): string
    {
        return $this->name;
    }
}
