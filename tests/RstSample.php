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

namespace App\Tests;

final class RstSample
{
    private $lineNumber = 0;

    private $content;

    public function __construct($content, int $lineNumber = 0)
    {
        if (!\is_array($content)) {
            $content = explode(PHP_EOL, $content);
        }

        $this->content = $content;
        $this->lineNumber = $lineNumber;
    }

    public function getLineNumber(): int
    {
        return $this->lineNumber;
    }

    public function getContent(): \ArrayIterator
    {
        return new \ArrayIterator($this->content);
    }
}
