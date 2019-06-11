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

use App\Value\RuleName;

interface Rule
{
    const TYPE_LINE = 1;
    const TYPE_FILE = 2;

    public static function getName(): RuleName;

    public static function getGroups(): array;

    public function check(\ArrayIterator $lines, int $number);

    public static function getType(): int;

    public static function runOnlyOnBlankline(): bool;
}
