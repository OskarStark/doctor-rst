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

use App\Handler\Registry;
use Webmozart\Assert\Assert;

final class RuleGroup
{
    /**
     * @var string
     */
    private $name;

    private function __construct(string $name)
    {
        Assert::notEmpty($name);
        Assert::oneOf($name, [
            Registry::GROUP_SONATA,
            Registry::GROUP_SYMFONY,
            Registry::GROUP_EXPERIMENTAL,
        ]);

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
