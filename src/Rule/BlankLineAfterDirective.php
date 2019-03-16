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

class BlankLineAfterDirective extends AbstractRule implements Rule
{
    public static function getGroups(): array
    {
        return [RulesHandler::GROUP_SONATA, RulesHandler::GROUP_SYMFONY];
    }

    public function check(\ArrayIterator $lines, int $number)
    {
        $lines->seek($number);
        $line = $lines->current();

        if (!RstParser::isDirective($line)) {
            return;
        }

        foreach (self::unSupportedDirectives() as $type) {
            if (RstParser::directiveIs($line, $type)) {
                return;
            }
        }

        $lines->next();

        // check if next line is empty
        $nextLine = $lines->current();

        if (!RstParser::isBlankLine($nextLine)) {
            return sprintf('Please add a blank line after "%s" directive', $line);
        }
    }

    public static function unSupportedDirectives()
    {
        return [
            RstParser::DIRECTIVE_INDEX,
            RstParser::DIRECTIVE_TOCTREE,
        ];
    }
}
