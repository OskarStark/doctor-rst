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

final class ExcludedViolationList
{
    /** @var Violation[] */
    private array $violations;
    private bool $hasViolations;
    private array $excludedViolationConfig;

    public function __construct(array $excludedViolationConfig, array $violations)
    {
        $this->violations = $this->filterViolations($violations);
        $this->hasViolations = \count($this->violations) > 0;
        $this->excludedViolationConfig = $excludedViolationConfig;
    }

    public function violations(): array
    {
        return $this->violations;
    }

    public function hasViolations(): bool
    {
        return $this->hasViolations;
    }

    private function filterViolations(array $violations): array
    {
        foreach ($violations as $key => $violation) {
            if (isset($this->excludedViolationConfig['regex'])) {
                foreach ($this->excludedViolationConfig['regex'] as $pattern) {
                    if (preg_match($pattern, $violation[3])) {
                        unset($violations[$key]);

                        break;
                    }
                }
            }

            if (isset($this->excludedViolationConfig['lines'])) {
                foreach ($this->excludedViolationConfig['lines'] as $line) {
                    if ($line === $violation[3]) {
                        unset($violations[$key]);

                        break;
                    }
                }
            }
        }

        return $violations;
    }
}
