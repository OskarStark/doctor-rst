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

use App\Value\Lines;
use App\Value\RuleGroup;
use App\Value\RuleName;

interface Rule
{
    /**
     * Rules using this type run for every line of the file content.
     */
    public const TYPE_LINE_CONTENT = 1;

    /**
     * Rules using this type are only run once, and are responsible
     * to check the whole file content on its own, if needed. They always
     * start on the first line of the document.
     */
    public const TYPE_FILE_CONTENT = 2;

    public static function getName(): RuleName;

    /**
     * @return RuleGroup[]
     */
    public static function getGroups(): array;

    public function check(Lines $lines, int $number): ?string;

    public static function getType(): int;

    public static function runOnlyOnBlankline(): bool;

    public static function isExperimental(): bool;
}
