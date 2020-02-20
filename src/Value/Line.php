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

use App\Rst\RstParser;
use function Symfony\Component\String\u;

final class Line
{
    private string $raw;
    private string $clean;
    private bool $blank;
    private ?int $indention = null;

    public function __construct(string $line)
    {
        $this->raw = $line;
        $this->clean = RstParser::clean($line);
        $this->blank = '' === $this->clean;
    }

    public function clean(): string
    {
        return $this->clean;
    }

    public function raw(): string
    {
        return $this->raw;
    }

    public function isBlank(): bool
    {
        return $this->blank;
    }

    public function indention(): int
    {
        if (null === $this->indention) {
            if ($matches = u($this->raw)->match('/^[\s]+/')) {
                return $this->indention = \strlen($matches[0]);
            }

            return $this->indention = 0;
        }

        return $this->indention;
    }
}
