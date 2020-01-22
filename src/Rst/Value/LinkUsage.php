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

final class LinkUsage
{
    /** @var LinkName */
    private $name;

    private function __construct(LinkName $name)
    {
        $this->name = $name;
    }

    public static function fromLine(string $line): self
    {
        preg_match('/(`[^`]+`|\S+)_/', $line, $matches);
        $matches[1] = trim($matches[1], '`');

        return new self(LinkName::fromString($matches[1]));
    }

    public static function fromLinkName(LinkName $name): self
    {
        return new self($name);
    }

    public function name(): LinkName
    {
        return $this->name;
    }
}
