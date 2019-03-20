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
use App\Rst\RstParser;

class Replacement extends AbstractRule implements Rule
{
    public static function getGroups(): array
    {
        return [RulesHandler::GROUP_SONATA, RulesHandler::GROUP_SYMFONY];
    }

    public function check(\ArrayIterator $lines, int $number)
    {
        $lines->seek($number);
        $line = $lines->current();

        $line = RstParser::clean($line);

        if (preg_match('/^([\s]+)?\/\/.\.(\.)?$/', $line, $matches)) {
            return sprintf('Please replace "%s" with "// ..."', $matches[0]);
        }

        if (preg_match('/^([\s]+)?#.\.(\.)?$/', $line, $matches)) {
            return sprintf('Please replace "%s" with "# ..."', $matches[0]);
        }

        if (preg_match('/^([\s]+)?<!--(.\.(\.)?|[\s]+\.\.[\s]+)-->$/', $line, $matches)) {
            return sprintf('Please replace "%s" with "<!-- ... -->"', $matches[0]);
        }
    }
}
