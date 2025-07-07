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

/**
 * @no-named-arguments
 */
interface Cache
{
    /**
     * @param Rule[] $rules
     */
    public function has(\SplFileInfo $file, array $rules): bool;

    /**
     * @param Rule[] $rules
     *
     * @return Violation[]
     */
    public function get(\SplFileInfo $file, array $rules): array;

    /**
     * @param Rule[]      $rules
     * @param Violation[] $violations
     */
    public function set(\SplFileInfo $file, array $rules, array $violations): void;

    public function write(): void;
}
