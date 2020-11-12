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

use App\Value\Lines;
use App\Value\RuleGroup;

class StringReplacement extends CheckListRule
{
    public static function getGroups(): array
    {
        return [
            RuleGroup::Symfony(),
        ];
    }

    public function check(Lines $lines, int $number): ?string
    {
        $lines->seek($number);
        $line = $lines->current();

        if ($line->clean()->containsAny($this->search)) {
            return sprintf($this->message, $this->search);
        }

        return null;
    }

    /**
     * @return array<string, string>
     */
    public static function getList(): array
    {
        return [
            '**type**: ``int``' => 'Please replace "%s" with "**type**: ``integer``"',
            '**type**: ``bool``' => 'Please replace "%s" with "**type**: ``boolean``"',
        ];
    }
}
