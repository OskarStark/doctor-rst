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

use App\Annotations\Rule\Description;
use App\Handler\Registry;
use App\Value\RuleGroup;

/**
 * @Description("Do not use belittling words!")
 */
class BeKindToNewcomers extends CheckListRule
{
    public static function getGroups(): array
    {
        return [RuleGroup::fromString(Registry::GROUP_EXPERIMENTAL)];
    }

    public function check(\ArrayIterator $lines, int $number)
    {
        $lines->seek($number);
        $line = $lines->current();

        if (preg_match($this->pattern, $line, $matches)) {
            return sprintf($this->message, $matches[0]);
        }
    }

    public function getDefaultMessage(): string
    {
        return 'Please remove the word: %s';
    }

    public static function getList(): array
    {
        return [
            '/simply/i' => null,
            '/easy/i' => null,
            '/easily/i' => null,
            '/obvious/i' => null,
            '/trivial/i' => null,
            '/just/i' => null,
            '/quick/i' => null,
            '/of course/i' => null,
            '/logically/i' => null,
            '/clear/i' => null,
            '/merely/i' => null,
            '/basic/i' => null,
        ];
    }
}
