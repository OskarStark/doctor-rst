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
use App\Rst\RstParser;
use App\Value\Lines;
use App\Value\RuleGroup;

/**
 * @Description("Ensure that no directive follows the shorthand `::`. This could lead to broken markup.")
 */
class NoDirectiveAfterShorthand extends AbstractRule implements LineContentRule
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

        if (!$line->raw()->endsWith(RstParser::SHORTHAND)) {
            return null;
        }

        $lines->next();

        while ($lines->valid() && $lines->current()->isBlank()) {
            $lines->next();
        }

        if (!$lines->current()->isDirective()) {
            return null;
        }

        return sprintf(
            'A "%s" directive is following a shorthand notation "%s", this will lead to a broken markup!',
            $lines->current()->clean()->toString(),
            RstParser::SHORTHAND
        );
    }
}
