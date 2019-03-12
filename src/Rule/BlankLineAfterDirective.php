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

        $supports = false;
        foreach ($this->supportedDirectives() as $type) {
            if (RstParser::directiveIs($line, $type)) {
                $supports = true;
                break;
            }
        }

        if (!$supports) {
            return;
        }

        $lines->next();

        // check if next line is empty
        $nextLine = $lines->current();

        if (!RstParser::isBlankLine($nextLine)) {
            return sprintf('Please add a blank line after "%s" directive', $line);
        }
    }

    private function supportedDirectives()
    {
        return [
            RstParser::DIRECTIVE_CODE_BLOCK,
            RstParser::DIRECTIVE_NOTE,
            RstParser::DIRECTIVE_WARNING,
            RstParser::DIRECTIVE_NOTICE,
            RstParser::DIRECTIVE_TIP,
            RstParser::DIRECTIVE_CAUTION,
            RstParser::DIRECTIVE_VERSIONADDED,
        ];
    }
}
