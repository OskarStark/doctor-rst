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

final class LinkDefinition
{
    /** @var LinkName */
    private $name;

    /** @var LinkUrl */
    private $url;

    private function __construct(LinkName $name, LinkUrl $url)
    {
        $this->name = $name;
        $this->url = $url;
    }

    public static function fromLine(string $line): self
    {
        preg_match('/^\.\. _`?([^`]+)`?: (.*)$/', $line, $matches);

        return new self(
            LinkName::fromString($matches[1]),
            LinkUrl::fromString($matches[2])
        );
    }

    public static function fromValues(LinkName $name, LinkUrl $url): self
    {
        return new self($name, $url);
    }

    public function name(): LinkName
    {
        return $this->name;
    }

    public function url(): LinkUrl
    {
        return $this->url;
    }
}
