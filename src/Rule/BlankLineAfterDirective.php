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

namespace App\Rule;

use App\Handler\RulesHandler;
use App\Util\Util;

class BlankLineAfterDirective implements Rule
{
    public static function getName(): string
    {
        return 'blank_line_after_directive';
    }

    public static function getGroups(): array
    {
        return [RulesHandler::GROUP_SONATA, RulesHandler::GROUP_SYMFONY];
    }

    public function check(\ArrayIterator $lines, int $number)
    {
        $lines->seek($number);
        $line = $lines->current();

        if (!Util::isDirective($line)) {
            return;
        }

        $supports = false;
        foreach ($this->supportedDirectives() as $type) {
            if (Util::directiveIs($line, $type)) {
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

        if (!empty(Util::clean($nextLine))) {
            return sprintf('Please add a blank line after "%s" directive', $line);
        }
    }

    private function supportedDirectives()
    {
        return [
            Util::DIRECTIVE_CODE_BLOCK,
            Util::DIRECTIVE_NOTE,
            Util::DIRECTIVE_WARNING,
            Util::DIRECTIVE_NOTICE,
            Util::DIRECTIVE_TIP,
            Util::DIRECTIVE_CAUTION,
            Util::DIRECTIVE_VERSIONADDED,
        ];
    }
}
