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
use App\Annotations\Rule\InvalidExample;
use App\Annotations\Rule\ValidExample;
use App\Rst\RstParser;
use App\Value\Lines;
use App\Value\RuleGroup;

/**
 * @Description("Ensure exactly one space between link definition and link.")
 *
 * @InvalidExample(".. _DOCtor-RST:     https://github.com/OskarStark/DOCtor-RST")
 *
 * @ValidExample(".. _DOCtor-RST: https://github.com/OskarStark/DOCtor-RST")
 */
class EnsureExactlyOneSpaceBetweenLinkDefinitionAndLink extends AbstractRule implements LineContentRule
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

        if (!RstParser::isLinkDefinition($line)) {
            return null;
        }

        if ($line->clean()->containsAny(':  ')) {
            return 'Please use only one whitespace between the link definition and the link.';
        }

        return null;
    }
}
