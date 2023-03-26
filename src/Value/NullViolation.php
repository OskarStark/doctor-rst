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

final class NullViolation implements ViolationInterface
{
    private string $message;
    private string $filename;
    private int $lineno;
    private string $rawLine;

    private function __construct()
    {
        $this->message = '';
        $this->filename = '';
        $this->lineno = 0;
        $this->rawLine = '';
    }

    public static function create(): self
    {
        return new self();
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
        return true;
    }
}
