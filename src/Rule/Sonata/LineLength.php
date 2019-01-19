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
use App\Rst\RstParser;
use App\Rule\Rule;

class LineLength implements Rule
{
    public static function getName(): string
    {
        return 'line_length';
    }

    public static function getGroups(): array
    {
        return [RulesHandler::GROUP_DEV];
    }

    public function check(\ArrayIterator $lines, int $number)
    {
        $lines->seek($number);
        $line = $lines->current();

        $count = mb_strlen(RstParser::clean($line));

        if ($count > $max = 80) {
            return sprintf('Line is to long (max %s) currently: %s', $max, $count);
        }
    }
}
