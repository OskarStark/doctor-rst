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

class LowercaseAsInUseStatements extends AbstractRule implements Rule
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

        if (!RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_PHP)
            && !RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_PHP_ANNOTATIONS)
        ) {
            return;
        }

        $indention = RstParser::indention($line);

        $lines->next();

        while ($lines->valid()
            && !RstParser::isDirective($lines->current())
            && ($indention < RstParser::indention($lines->current()) || RstParser::isBlankLine($lines->current()))
        ) {
            if (preg_match('/^use (.*) (AS|As|aS) (.*);$/', RstParser::clean($lines->current()), $matches)) {
                return sprintf('Please use lowercase "as" instead of "%s"', $matches[2]);
            }

            $lines->next();
        }
    }
}
