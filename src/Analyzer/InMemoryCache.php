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

use App\Value\Violation;

final class InMemoryCache implements Cache
{
    /**
     * @var array<string, Violation[]>
     */
    private array $cache = [];

    public function has(\SplFileInfo $file, array $rules): bool
    {
        return isset($this->cache[$file->getPathname()]);
    }

    public function get(\SplFileInfo $file, array $rules): array
    {
        return $this->cache[$file->getPathname()] ?? [];
    }

    public function set(\SplFileInfo $file, array $rules, array $violations): void
    {
        $this->cache[$file->getPathname()] = $violations;
    }

    public function write(): void
    {
    }
}
