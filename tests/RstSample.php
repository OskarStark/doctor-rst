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

use App\Value\Lines;

final class RstSample
{
    private int $lineNumber = 0;

    /**
     * @var array<string>
     */
    private $lines;

    /**
     * @param string|array<string> $content
     */
    public function __construct($content, int $lineNumber = 0)
    {
        if (!\is_array($content)) {
            $content = explode(PHP_EOL, $content);
        }

        $this->lines = Lines::fromArray($content);
        $this->lineNumber = $lineNumber;
    }

    public function lineNumber(): int
    {
        return $this->lineNumber;
    }

    public function lines(): Lines
    {
        return $this->lines;
    }
}
