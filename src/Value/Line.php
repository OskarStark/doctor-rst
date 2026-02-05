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

namespace App\Value;

use Symfony\Component\String\UnicodeString;
use function Symfony\Component\String\u;

final class Line
{
    private readonly UnicodeString $raw;
    private readonly UnicodeString $clean;
    private readonly bool $blank;
    private ?int $indention = null;
    private ?bool $headline = null;
    private ?bool $isDirective = null;
    private ?bool $isDefaultDirective = null;

    /**
     * @var string[]
     */
    private array $processedBy = [];

    public function __construct(string $line)
    {
        $this->raw = u($line);
        $this->clean = self::cleanString($this->raw);
        $this->blank = $this->clean->isEmpty();
    }

    public function raw(): UnicodeString
    {
        return $this->raw;
    }

    public function clean(): UnicodeString
    {
        return $this->clean;
    }

    public function isBlank(): bool
    {
        return $this->blank;
    }

    public function indention(): int
    {
        if (null === $this->indention) {
            if ($matches = $this->raw->match('/^[\s]+/')) {
                /** @var string[] $matches */
                return $this->indention = \strlen($matches[0]);
            }

            return $this->indention = 0;
        }

        return $this->indention;
    }

    public function isHeadline(): bool
    {
        if (null === $this->headline) {
            $this->headline = [] !== $this->raw->match('/^([\=]+|[\~]+|[\*]+|[\-]+|[\.]+|[\^]+)$/');
        }

        return $this->headline;
    }

    public function isDirective(): bool
    {
        if (null === $this->isDirective) {
            $string = ltrim($this->raw->toString());
            $len = \strlen($string);

            if (2 <= $len && '.' === $string[0] && '.' === $string[1] && (!str_starts_with($string, '.. _`') && str_contains($string, '::'))
            ) {
                return $this->isDirective = true;
            }

            if ($this->isDefaultDirective()) {
                return $this->isDirective = true;
            }

            $this->isDirective = false;
        }

        return $this->isDirective;
    }

    public function isDefaultDirective(): bool
    {
        if (null === $this->isDefaultDirective) {
            $string = rtrim($this->raw->toString());
            $len = \strlen($string);

            if (2 > $len || $string[$len - 1] !== ':' || $string[$len - 2] !== ':') {
                return $this->isDefaultDirective = false;
            }

            $this->isDefaultDirective = !preg_match('/^\.\. (.*)::/', $this->raw->toString());
        }

        return $this->isDefaultDirective;
    }

    public function markProcessedBy(string $rule): void
    {
        $this->processedBy[] = $rule;
    }

    public function isProcessedBy(string $rule): bool
    {
        return \in_array($rule, $this->processedBy, true);
    }

    private static function cleanString(UnicodeString $string): UnicodeString
    {
        $clean = $string->trim()->toUnicodeString();

        if ($clean->endsWith('\n')) {
            $clean = $clean->slice(0, -2)->toUnicodeString();
        }

        if ($clean->endsWith('\r')) {
            $clean = $clean->slice(0, -2)->toUnicodeString();
        }

        return $clean->trim()->toUnicodeString();
    }
}
