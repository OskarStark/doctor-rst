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

    public function addRuleForAll(Rule $rule): void
    {
        $this->rulesForAll[] = $rule;
    }

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
    public function getRulesForFilePath(string $filePath): array
    {
        return $this->rulesForAll;
    }
}
