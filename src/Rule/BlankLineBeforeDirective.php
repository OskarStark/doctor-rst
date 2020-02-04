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
use App\Rst\RstParser;
use App\Value\Lines;
use App\Value\RuleGroup;

/**
 * @Description("Make sure you have a blank line before each directive.")
 */
class BlankLineBeforeDirective extends AbstractRule implements Rule
{
    public static function getGroups(): array
    {
        return [
            RuleGroup::fromString(Registry::GROUP_SONATA),
            RuleGroup::fromString(Registry::GROUP_SYMFONY),
        ];
    }

    public function check(Lines $lines, int $number): ?string
    {
        $lines = $lines->toIterator();

        $lines->seek($number);
        $line = $lines->current();

        if (0 == $number) {
            // it is ok to start with a directive
            return null;
        }

        if (RstParser::isDefaultDirective($line) || !RstParser::isDirective($line)) {
            return null;
        }

        $lines->seek($number - 1);

        if ($lines->valid()
            && !RstParser::isBlankLine($lines->current())
            && !RstParser::directiveIs($lines->current(), RstParser::DIRECTIVE_CLASS)
            && !RstParser::isComment($lines->current())
        ) {
            return sprintf('Please add a blank line before "%s" directive', $line);
        }

        return null;
    }
}
