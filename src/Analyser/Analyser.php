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

namespace App\Analyser;

use App\Rule\Rule;
use App\Value\Violation;
use SplFileInfo;

interface Analyser
{
    /**
     * @param Rule[] $rules
     *
     * @return Violation[]
     */
    public function analyse(SplFileInfo $file, array $rules): array;
}
