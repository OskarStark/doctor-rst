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

interface Rule
{
    public static function getName(): string;

    public static function getGroups(): array;

    public function check(\ArrayIterator $lines, int $number);
}
