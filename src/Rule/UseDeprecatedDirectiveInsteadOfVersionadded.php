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

use App\Handler\Registry;
use App\Rst\RstParser;
use App\Value\RuleGroup;

class UseDeprecatedDirectiveInsteadOfVersionadded extends AbstractRule implements Rule
{
    public static function getGroups(): array
    {
        return [
            RuleGroup::fromString(Registry::GROUP_SONATA),
            RuleGroup::fromString(Registry::GROUP_SYMFONY),
        ];
    }

    public function check(\ArrayIterator $lines, int $number)
    {
        $lines->seek($number);
        $line = $lines->current();

        if (!RstParser::directiveIs($line, RstParser::DIRECTIVE_VERSIONADDED)) {
            return;
        }

        $indention = RstParser::indention($line);

        $lines->next();

        while ($lines->valid()
            && ($indention < RstParser::indention($lines->current()) || RstParser::isBlankLine($lines->current()))
        ) {
            if (preg_match('/[^`]deprecated/', $lines->current())) {
                return 'Please use ".. deprecated::" instead of ".. versionadded::"';
            }

            $lines->next();
        }
    }
}
