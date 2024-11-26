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

final readonly class LinkUsage
{
    private function __construct(
        private LinkName $name,
    ) {
    }

    public static function fromLine(string $line): self
    {
        preg_match('/(`[^`]+`|(?:(?!_)\w)+(?:[-._+:](?:(?!_)\w)+)*+)_/', $line, $matches);
        Assert::keyExists($matches, 1);
        Assert::string($matches[1]);
        $name = trim($matches[1], '`');

        return new self(LinkName::fromString($name));
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
