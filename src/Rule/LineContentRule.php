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

/**
 * Rules using this interface run for every line of the file content.
 */
interface LineContentRule extends Rule
{
    public function check(Lines $lines, int $number): ?string;
}
