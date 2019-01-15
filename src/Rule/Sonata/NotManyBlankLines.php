<?php

declare(strict_types=1);

/*
 * This file is part of the rst-checker.
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

class NotManyBlankLines implements Rule
{
    public static function getName(): string
    {
        return 'not_many_blank_lines';
    }

    public static function getGroups(): array
    {
        return [RulesHandler::GROUP_SONATA];
    }

    public function check(\ArrayIterator $lines, int $number)
    {
        $lines->seek($number);
        $line = $lines->current();

        if (!RstParser::isBlankLine($line)) {
            return;
        }

        $lines->next();
        $nextLine = $lines->current();

        if (null === $nextLine) {
            return;
        }

        if (RstParser::isBlankLine($nextLine)) {
            return 'Please avoid many blank lines';
        }
    }
}
