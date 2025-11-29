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

namespace App\Rst\Value;

use Webmozart\Assert\Assert;

final readonly class LinkDefinition
{
    private function __construct(
        private LinkName $name,
        private LinkUrl $url,
    ) {
    }

    public static function fromLine(string $line): self
    {
        preg_match('/^\s*\.\. _`?([^`]+)`?: (.*)$/', $line, $matches);
        Assert::keyExists($matches, 1);
        Assert::keyExists($matches, 2);

        return new self(
            LinkName::fromString($matches[1]),
            LinkUrl::fromString($matches[2]),
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
