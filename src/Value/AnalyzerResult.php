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

namespace App\Value;

final class AnalyzerResult
{
    /** @var FileResult[] */
    private array $results;

    /**
     * @param FileResult[] $fileResults
     */
    public function __construct(array $fileResults)
    {
        $this->results = $fileResults;
    }

    public function all(): array
    {
        return $this->results;
    }

    public function hasViolations(): bool
    {
        foreach ($this->results as $fileResult) {
            if ($fileResult->violationList()->hasViolations()) {
                return true;
            }
        }

        return false;
    }
}
