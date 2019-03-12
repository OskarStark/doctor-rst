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
    public $search;
    public $message;

    public function configure(string $search, string $message): self
    {
        $this->search = $search;
        $this->message = sprintf($message, $search);

        return $this;
    }

    public static function getList(): array
    {
        return [];
    }
}
