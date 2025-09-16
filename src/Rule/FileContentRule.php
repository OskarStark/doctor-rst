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

use App\Value\Lines;
use App\Value\ViolationInterface;

/**
 * Rules using this interface are only run once per file, and are
 * responsible to check the whole file content on its own, if needed.
 * They always start on the first line of the document.
 *
 * @no-named-arguments
 */
interface FileContentRule extends Rule
{
    public function check(Lines $lines, string $filename): ViolationInterface;
}
