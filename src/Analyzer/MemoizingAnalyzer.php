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

final readonly class MemoizingAnalyzer implements Analyzer
{
    public function __construct(
        private Analyzer $analyzer,
        private Cache $cache,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function analyze(\SplFileInfo $file, array $rules): array
    {
        if ($this->cache->has($file, $rules)) {
            return $this->cache->get($file, $rules);
        }

        $violations = $this->analyzer->analyze($file, $rules);
        $this->cache->set($file, $rules, $violations);

        return $violations;
    }

    public function write(): void
    {
        $this->cache->write();
    }
}
