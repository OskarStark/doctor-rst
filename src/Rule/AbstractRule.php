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
    public static function getName(): RuleName
    {
        return RuleName::fromClassString(static::class);
    }

    /**
     * @return RuleGroup[]
     */
    public static function getGroups(): array
    {
        return [];
    }

    public static function runOnlyOnBlankline(): bool
    {
        return false;
    }

    public static function isExperimental(): bool
    {
        return array_any(static::getGroups(), static fn ($group) => $group->equals(RuleGroup::Experimental()));
    }
}
