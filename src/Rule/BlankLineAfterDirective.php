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
 * @Description("Make sure you have a blank line after each directive.")
 */
class BlankLineAfterDirective extends AbstractRule implements Rule
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
        $lines->seek($number);
        $line = $lines->current();

        if (!RstParser::isDirective($line)) {
            return null;
        }

        foreach (self::unSupportedDirectives() as $type) {
            if (RstParser::directiveIs($line, $type) || !\in_array($type, RstParser::DIRECTIVES, true)) {
                return null;
            }
        }

        $lines->next();

        if (RstParser::isOption($lines->current())) {
            $lines->next();
        }

        // check if next line is empty
        $nextLine = $lines->current();

        if ($lines->valid() && !$nextLine->isBlank()) {
            return sprintf('Please add a blank line after "%s" directive', $line->raw());
        }

        return null;
    }

    /**
     * @return array<int, string>
     */
    public static function unSupportedDirectives(): array
    {
        return [
            RstParser::DIRECTIVE_INDEX,
            RstParser::DIRECTIVE_TOCTREE,
            RstParser::DIRECTIVE_INCLUDE,
            RstParser::DIRECTIVE_IMAGE,
            RstParser::DIRECTIVE_ADMONITION,
            RstParser::DIRECTIVE_ROLE,
            RstParser::DIRECTIVE_FIGURE,
            RstParser::DIRECTIVE_CLASS,
            RstParser::DIRECTIVE_CONTENTS,
        ];
    }
}
