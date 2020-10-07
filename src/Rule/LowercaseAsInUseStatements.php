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

use App\Rst\RstParser;
use App\Value\Lines;
use App\Value\RuleGroup;

class LowercaseAsInUseStatements extends AbstractRule implements Rule
{
    public static function getGroups(): array
    {
        return [
            RuleGroup::Sonata(),
            RuleGroup::Symfony(),
        ];
    }

    public function check(Lines $lines, int $number): ?string
    {
        $lines->seek($number);
        $line = $lines->current();

        if (!RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_PHP)
            && !RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_PHP_ANNOTATIONS)
            && !RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_PHP_ATTRIBUTES)
        ) {
            return null;
        }

        $indention = $line->indention();

        $lines->next();

        while ($lines->valid()
            && !RstParser::isDirective($lines->current())
            && ($indention < $lines->current()->indention() || $lines->current()->isBlank())
        ) {
            if (preg_match('/^use (.*) (AS|As|aS) (.*);$/', $lines->current()->clean(), $matches)) {
                return sprintf('Please use lowercase "as" instead of "%s"', $matches[2]);
            }

            $lines->next();
        }

        return null;
    }
}
