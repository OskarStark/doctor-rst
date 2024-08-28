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

final readonly class ExcludedViolationList
{
    /**
     * @var Violation[]
     */
    private array $violations;
    private bool $hasViolations;

    /**
     * @var array<string, int>
     */
    private array $matchedWhitelistRegex;

    /**
     * @var array<string, int>
     */
    private array $matchedWhitelistLines;

    public function __construct(array $excludedViolationConfig, array $violations)
    {
        $filteredViolations = $this->filterViolations($excludedViolationConfig, $violations);

        $this->violations = $filteredViolations['violations'];
        $this->hasViolations = \count($this->violations) > 0;
        $this->matchedWhitelistRegex = $filteredViolations['matchedWhitelistRegex'];
        $this->matchedWhitelistLines = $filteredViolations['matchedWhitelistLines'];
    }

    public function violations(): array
    {
        return $this->violations;
    }

    public function hasViolations(): bool
    {
        return $this->hasViolations;
    }

    /**
     * @return array<string, int>
     */
    public function getMatchedWhitelistRegex(): array
    {
        return $this->matchedWhitelistRegex;
    }

    /**
     * @return array<string, int>
     */
    public function getMatchedWhitelistLines(): array
    {
        return $this->matchedWhitelistLines;
    }

    /**
     * @param Violation[] $violations
     *
     * @return array{violations: Violation[], matchedWhitelistRegex: array<string, int>, matchedWhitelistLines: array<string, int>}
     */
    private function filterViolations(array $excludedViolationConfig, array $violations): array
    {
        $matchedWhitelistRegex = [];
        $matchedWhitelistLines = [];

        foreach ($violations as $key => $violation) {
            if (isset($excludedViolationConfig['regex'])) {
                /** @var string $pattern */
                foreach ($excludedViolationConfig['regex'] as $pattern) {
                    if (preg_match($pattern, $violation->rawLine())) {
                        $matchedWhitelistRegex[$pattern] = isset($matchedWhitelistRegex[$pattern]) ? 1 + $matchedWhitelistRegex[$pattern] : 1;
                        unset($violations[$key]);

                        break;
                    }
                }
            }

            if (isset($excludedViolationConfig['lines'])) {
                /** @var string $line */
                foreach ($excludedViolationConfig['lines'] as $line) {
                    if ($violation->rawLine() === $line) {
                        $matchedWhitelistLines[$line] = isset($matchedWhitelistLines[$line]) ? 1 + $matchedWhitelistLines[$line] : 1;
                        unset($violations[$key]);

                        break;
                    }
                }
            }
        }

        return [
            'violations' => $violations,
            'matchedWhitelistRegex' => $matchedWhitelistRegex,
            'matchedWhitelistLines' => $matchedWhitelistLines,
        ];
    }
}
