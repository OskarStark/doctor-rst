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

use Webmozart\Assert\Assert;

final class Violation implements ViolationInterface
{
    private readonly string $message;
    private readonly string $filename;
    private readonly int $lineno;

    private function __construct(string $message, string $filename, int $lineno, private readonly string $rawLine)
    {
        $message = trim($message);
        Assert::stringNotEmpty($message);
        Assert::notWhitespaceOnly($message);

        $filename = trim($filename);
        Assert::stringNotEmpty($filename);
        Assert::notWhitespaceOnly($filename);

        Assert::greaterThan($lineno, 0);

        $this->message = $message;
        $this->filename = $filename;
        $this->lineno = $lineno;
    }

    public static function from(string $message, string $filename, int $lineno, Line|string $rawLine): self
    {
        return new self($message, $filename, $lineno, $rawLine instanceof Line ? $rawLine->clean()->toString() : $rawLine);
    }

    public function message(): string
    {
        return $this->message;
    }

    public function filename(): string
    {
        return $this->filename;
    }

    public function lineno(): int
    {
        return $this->lineno;
    }

    public function rawLine(): string
    {
        return $this->rawLine;
    }

    public function isNull(): bool
    {
        return false;
    }
}
