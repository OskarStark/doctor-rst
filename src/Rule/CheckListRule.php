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

namespace App\Rule;

abstract class CheckListRule extends AbstractRule implements Rule
{
    public string $pattern;
    public ?string $message;

    public function configure(string $pattern, ?string $message): self
    {
        $this->pattern = $pattern;
        $this->message = $message ?: $this->getDefaultMessage();

        return $this;
    }

    abstract public function getDefaultMessage(): string;

    /**
     * @return array<string, string|null>
     */
    public static function getList(): array
    {
        return [];
    }
}
