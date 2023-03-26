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

namespace App\Analyzer;

use App\Rule\Rule;
use App\Value\Violation;

interface Analyzer
{
    /**
     * @param Rule[] $rules
     *
     * @return Violation[]
     */
    public function analyze(\SplFileInfo $file, array $rules): array;
}
