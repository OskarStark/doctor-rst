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

/**
 * @no-named-arguments
 */
interface Rule
{
    public static function getName(): RuleName;

    /**
     * @return RuleGroup[]
     */
    public static function getGroups(): array;

    public static function runOnlyOnBlankline(): bool;

    public static function isExperimental(): bool;
}
