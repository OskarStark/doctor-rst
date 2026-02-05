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

namespace App\Value;

final class AnalyzerResult
{
    /**
     * @param FileResult[]                              $results
     * @param array{regex?: string[], lines?: string[]} $whitelistConfig
     */
    public function __construct(
        private readonly array $results,
        private array $whitelistConfig,
    ) {
    }

    /**
     * @return FileResult[]
     */
    public function all(): array
    {
        return $this->results;
    }

    public function hasViolations(): bool
    {
        return array_any($this->results, static fn ($fileResult) => $fileResult->violationList()->hasViolations());
    }

    /**
     * @return array{regex: string[], lines: string[]}
     */
    public function getUnusedWhitelistRules(): array
    {
        $unused = [
            'regex' => [],
            'lines' => [],
        ];

        [$matchedRegex, $matchedLines] = $this->getMatchedWhitelistRules();

        foreach ($this->whitelistConfig['regex'] ?? [] as $regex) {
            if (!\array_key_exists($regex, $matchedRegex)) {
                $unused['regex'][] = $regex;
            }
        }

        foreach ($this->whitelistConfig['lines'] ?? [] as $line) {
            if (!\array_key_exists($line, $matchedLines)) {
                $unused['lines'][] = $line;
            }
        }

        return $unused;
    }

    /**
     * @return array{0: array<string, int>, 1: array<string, int>}
     */
    private function getMatchedWhitelistRules(): array
    {
        $allMatchedRegex = [];
        $allMatchedLines = [];

        foreach ($this->results as $fileResult) {
            foreach ($fileResult->violationList()->getMatchedWhitelistRegex() as $pattern => $count) {
                $allMatchedRegex[$pattern] = isset($allMatchedRegex[$pattern]) ? $count + $allMatchedRegex[$pattern] : $count;
            }

            foreach ($fileResult->violationList()->getMatchedWhitelistLines() as $line => $count) {
                $allMatchedLines[$line] = isset($allMatchedLines[$line]) ? $count + $allMatchedLines[$line] : $count;
            }
        }

        return [$allMatchedRegex, $allMatchedLines];
    }
}
