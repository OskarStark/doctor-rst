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

namespace App\Rule\Sonata;

use App\Handler\RulesHandler;
use App\Rule\Rule;

class ShortArraySyntax implements Rule
{
    public static function getName(): string
    {
        return 'short_array_syntax';
    }

    public static function getGroups(): array
    {
        return [RulesHandler::GROUP_SONATA];
    }

    public function check(\ArrayIterator $lines, int $number)
    {
        $lines->seek($number);
        $line = $lines->current();

        if (preg_match('/ array\(/', $line)) {
            return 'Please use short array syntax';
        }
    }
}
