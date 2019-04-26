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
 * @Description("Ensure that only 0x20 (regular whitespace) and no 0xA0 (see http://www.fileformat.info/info/unicode/char/00a0/index.htm) is used.")
 */
class ValidWhitespace extends AbstractRule implements Rule
{
    public static function getGroups(): array
    {
        return [RuleGroup::fromString(Registry::GROUP_EXPERIMENTAL)];
    }

    public function check(\ArrayIterator $lines, int $number)
    {
        $lines->seek($number);
        $line = $lines->current();

        if ($line != str_replace('a0', ' ', $line)) {
            return 'no';
        }
    }
}
