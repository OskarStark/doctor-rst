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

interface Cache
{
    public function has(\SplFileInfo $file, array $rules): bool;

    public function get(\SplFileInfo $file, array $rules): array;

    public function set(\SplFileInfo $file, array $rules, array $violations): void;

    public function write(): void;
}
