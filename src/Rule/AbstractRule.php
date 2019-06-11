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

use App\Value\RuleGroup;
use App\Value\RuleName;
use Doctrine\Common\Inflector\Inflector;

abstract class AbstractRule
{
    public static function getName(): RuleName
    {
        return RuleName::fromString(
            Inflector::tableize(substr((string) strrchr(static::class, '\\'), 1))
        );
    }

    /**
     * @return RuleGroup[]
     */
    public static function getGroups(): array
    {
        return [];
    }

    public static function getType(): int
    {
        return Rule::TYPE_LINE;
    }

    public static function runOnlyOnBlankline(): bool
    {
        return false;
    }
}
