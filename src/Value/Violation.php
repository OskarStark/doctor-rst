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

use Webmozart\Assert\Assert;

final class Violation
{
    private $message;
    private $filename;
    private $lineno;

    private function __construct(string $message, string $filename, int $lineno)
    {
        Assert::notEmpty($message);
        Assert::notEmpty($filename);
        Assert::greaterThan($lineno, 0);

        $this->message = $message;
        $this->filename = $filename;
        $this->lineno = $lineno;
    }

    public static function from(string $message, string $filename, int $lineno): self
    {
        return new self($message, $filename, $lineno);
    }

    public function message(): string
    {
        return $this->message;
    }

    public function filename(): string
    {
        return $this->filename;
    }

    public function lineno(): string
    {
        return $this->lineno();
    }
}
