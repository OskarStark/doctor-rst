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

use App\Handler\RulesHandler;

class AmericanEnglish extends CheckListRule
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
        return 'Please use American English for: %s';
    }

    public static function getList(): array
    {
        return [
            '/(B|b)ehaviour(s)?/' => null,
        ];
    }
}
