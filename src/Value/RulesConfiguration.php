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

use App\Rule\Rule;

final class RulesConfiguration
{
    /**
     * @var Rule[]
     */
    private array $rulesForAll = [];

    /**
     * @var array<string, array<Rule>>
     */
    private array $excludedRulesByFilePath = [];

    public function addRuleForAll(Rule $rule): void
    {
        $this->rulesForAll[] = $rule;
    }

    /**
     * @param Rule[] $rules
     */
    public function setRulesForAll(array $rules): void
    {
        $this->rulesForAll = [];

        foreach ($rules as $rule) {
            $this->addRuleForAll($rule);
        }
    }

    public function hasRulesForAll(): bool
    {
        return [] !== $this->rulesForAll;
    }

    /**
     * @return Rule[]
     */
    public function getRulesForAll(): array
    {
        return $this->rulesForAll;
    }

    /**
     * @param Rule[] $rules
     */
    public function excludeRulesForFilePath(string $filePath, array $rules): void
    {
        foreach ($rules as $rule) {
            $this->excludeRuleForFilePath($filePath, $rule);
        }
    }

    public function excludeRuleForFilePath(string $filePath, Rule $rule): void
    {
        $this->excludedRulesByFilePath[$filePath][] = $rule;
    }

    /**
     * @return Rule[]
     */
    public function getRulesForFilePath(string $filePath): array
    {
        $excludedRulesForFile = $this->excludedRulesByFilePath[$filePath] ?? null;

        if ($excludedRulesForFile) {
            return array_udiff($this->rulesForAll, $excludedRulesForFile, static fn (Rule $a, Rule $b) => spl_object_id($a) <=> spl_object_id($b));
        }

        return $this->rulesForAll;
    }
}
