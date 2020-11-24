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
    public string $search;
    public string $message;

    /**
     * @return static
     */
    public function configure(string $pattern, ?string $message): self
    {
        $this->search = $pattern;
        $this->message = $message ?? static::getDefaultMessage();

        return $this;
    }

    public static function getDefaultMessage(): string
    {
        return 'Please don\'t use: %s';
    }

    /**
     * @return array<string, string|null>
     */
    abstract public static function getList(): array;
}
