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

use SplFileInfo;

final class MemoizingAnalyser implements Analyser
{
    private Analyser $analyser;
    private Cache $cache;

    public function __construct(Analyser $analyser, Cache $cache)
    {
        $this->analyser = $analyser;
        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function analyse(SplFileInfo $file, array $rules): array
    {
        if ($this->cache->has($file, $rules)) {
            return $this->cache->get($file, $rules);
        }

        $violations = $this->analyser->analyse($file, $rules);
        $this->cache->set($file, $rules, $violations);

        return $violations;
    }

    public function write(): void
    {
        $this->cache->write();
    }
}
