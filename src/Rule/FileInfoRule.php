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

use App\Value\ViolationInterface;

/**
 * Rules using this interface are only run once,
 * and get a \SplFileInfo containing infos of the file.
 */
interface FileInfoRule extends Rule
{
    public function check(\SplFileInfo $file): ViolationInterface;
}
