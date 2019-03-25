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
use App\Handler\RulesHandler;

/**
 * @Description("Do not use beliting words!")
 */
class BeKeenToNewcomers extends CheckListRule
{
    public static function getGroups(): array
    {
        return [RulesHandler::GROUP_SONATA, RulesHandler::GROUP_SYMFONY];
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
            '/(S|s)imply/' => null,
            '/(E|e)asy/' => null,
            '/(E|e)asily/' => null,
            '/(O|o)bviously/' => null,
            '/(T|t)rivial/' => null,
        ];
    }
}
