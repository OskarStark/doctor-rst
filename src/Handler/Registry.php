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

namespace App\Handler;

use App\Rule\CheckListRule;
use App\Rule\Rule;
use App\Value\RuleGroup;
use App\Value\RuleName;

/**
 * @no-named-arguments
 */
final class Registry
{
    /**
     * @var array<string, Rule>
     */
    private array $rules = [];

    /**
     * @var Rule[]
     */
    private array $rawRules = [];

    public function __construct(iterable $rules)
    {
        foreach ($rules as $rule) {
            \assert($rule instanceof Rule);

            $this->rawRules[] = $rule;

            if ($rule instanceof CheckListRule) {
                $i = 0;

                foreach ($rule::getList() as $search => $message) {
                    $clonedRule = clone $rule;
                    $this->rules[$rule::getName()->toString().'_'.$i] = $clonedRule->configure($search, $message);
                    ++$i;
                }

                continue;
            }

            $this->rules[$rule::getName()->toString()] = $rule;
        }
    }

    /**
     * @param array<string, Rule> $rules
     */
    public function setRules(array $rules): self
    {
        $this->rules = $rules;

        return $this;
    }

    /**
     * @return Rule[]
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * @return Rule[]
     */
    public function getRawRules(): array
    {
        return $this->rawRules;
    }

    /**
     * @return Rule[]
     */
    public function getRulesByGroup(RuleGroup $group): array
    {
        $rules = [];

        foreach ($this->rules as $rule) {
            if (\in_array($group, $rule::getGroups(), true)) {
                $rules[] = $rule;
            }
        }

        return $rules;
    }

    public function getRule(RuleName $name): Rule
    {
        if (!isset($this->rules[$name->toString()])) {
            throw new \InvalidArgumentException(\sprintf('Could not find rule: %s', $name->toString()));
        }

        return $this->rules[$name->toString()];
    }

    /**
     * @return Rule[]
     */
    public function getRulesByName(RuleName $name): array
    {
        $rules = [];

        try {
            $rules[] = $this->getRule($name);
        } catch (\InvalidArgumentException $e) {
            foreach ($this->rules as $key => $rule) {
                if (preg_match(\sprintf('/%s/', $name->toString()), $key)) {
                    $rules[] = $rule;
                }
            }

            if ([] === $rules) {
                throw $e;
            }
        }

        return $rules;
    }

    /**
     * @template T of Rule
     *
     * @param class-string<T> $type
     *
     * @return T[]
     */
    public function getRulesByType(string $type): array
    {
        return array_filter($this->rules, static fn (Rule $rule): bool => $rule instanceof $type);
    }
}

