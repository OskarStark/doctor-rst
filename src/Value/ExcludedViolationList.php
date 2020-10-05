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

    public function __construct(array $excludedViolationConfig, array $violations)
    {
        $this->violations = $this->filterViolations($excludedViolationConfig, $violations);
        $this->hasViolations = \count($this->violations) > 0;
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
     * @param Violation[] $violations
     *
     * @return Violation[]
     */
    private function filterViolations(array $excludedViolationConfig, array $violations): array
    {
        foreach ($violations as $key => $violation) {
            if (isset($excludedViolationConfig['regex'])) {
                foreach ($excludedViolationConfig['regex'] as $pattern) {
                    if (preg_match($pattern, $violation->rawLine())) {
                        unset($violations[$key]);

                        break;
                    }
                }
            }

            if (isset($excludedViolationConfig['lines'])) {
                foreach ($excludedViolationConfig['lines'] as $line) {
                    if ($line === $violation->rawLine()) {
                        unset($violations[$key]);

                        break;
                    }
                }
            }
        }

        return $violations;
    }
}
