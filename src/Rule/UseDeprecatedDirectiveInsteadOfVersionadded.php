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
use App\Value\Lines;
use App\Value\RuleGroup;
use function Symfony\Component\String\u;

class UseDeprecatedDirectiveInsteadOfVersionadded extends AbstractRule implements Rule
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

        if (!RstParser::directiveIs($line, RstParser::DIRECTIVE_VERSIONADDED)) {
            return null;
        }

        $indention = $line->indention();

        $lines->next();

        while ($lines->valid()
            && ($indention < $lines->current()->indention() || $lines->current()->isBlank())
        ) {
            if (u($lines->current()->raw())->match('/[^`]deprecated/')) {
                return 'Please use ".. deprecated::" instead of ".. versionadded::"';
            }

            $lines->next();
        }

        return null;
    }
}
