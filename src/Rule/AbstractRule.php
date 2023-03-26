<?php

declare(strict_types=1);

/**
 * This file is part of DOCtor-RST.
 *
 * (c) Oskar Stark <oskarstark@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Rule;

use App\Value\RuleGroup;
use App\Value\RuleName;

abstract class AbstractRule
{
    final public static function getName(): RuleName
    {
        return RuleName::fromClassString(static::class);
    }

    /**
     * @return RuleGroup[]
     */
    final public static function getGroups(): array
    {
        return [];
    }

    final public static function runOnlyOnBlankline(): bool
    {
        return false;
    }

    final public static function isExperimental(): bool
    {
        foreach (static::getGroups() as $group) {
            if ($group->equals(RuleGroup::Experimental())) {
                return true;
            }
        }

        return false;
    }
}
