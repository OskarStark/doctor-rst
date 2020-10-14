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
use Symfony\Component\String\UnicodeString;

final class Line
{
    private string $raw;
    private string $clean;
    private bool $blank;
    private ?int $indention = null;
    private ?UnicodeString $rawU = null;
    private ?UnicodeString $cleanU = null;
    private ?bool $headline = null;
    private ?bool $isDirective = null;
    private ?bool $isDefaultDirective = null;

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

    public function rawU(): UnicodeString
    {
        if (null === $this->rawU) {
            $this->rawU = u($this->raw);
        }

        return $this->rawU;
    }

    public function cleanU(): UnicodeString
    {
        if (null === $this->cleanU) {
            $this->cleanU = u($this->clean);
        }

        return $this->cleanU;
    }

    public function isBlank(): bool
    {
        return $this->blank;
    }

    public function indention(): int
    {
        if (null === $this->indention) {
            if ($matches = $this->rawU()->match('/^[\s]+/')) {
                return $this->indention = \strlen($matches[0]);
            }

            return $this->indention = 0;
        }

        return $this->indention;
    }

    public function isHeadline(): bool
    {
        if (null === $this->headline) {
            $this->headline = [] !== $this->rawU()->match('/^([\=]+|[\~]+|[\*]+|[\-]+|[\.]+|[\^]+)$/');
        }

        return $this->headline;
    }

    /**
     * @todo use regex here
     */
    public function isDirective(): bool
    {
        if (null === $this->isDirective) {
            $this->isDirective = (0 === strpos(ltrim($this->raw), '.. ')
                    && 0 !== strpos(ltrim($this->raw), '.. _`')
                    && false !== strpos($this->raw, '::')
                ) || $this->isDefaultDirective();
        }

        return $this->isDirective;
    }

    public function isDefaultDirective(): bool
    {
        if (null === $this->isDefaultDirective) {
            $this->isDefaultDirective = !preg_match('/^\.\. (.*)::/', $this->raw)
                && preg_match('/::$/', $this->raw);
        }

        return $this->isDefaultDirective;
    }
}
