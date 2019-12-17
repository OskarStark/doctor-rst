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

namespace App\Handler;

use App\Rule\CheckListRule;
use App\Rule\Rule;
use App\Value\RuleGroup;
use App\Value\RuleName;

final class Registry
{
    const GROUP_EXPERIMENTAL = '@Experimental';
    const GROUP_SONATA = '@Sonata';
    const GROUP_SYMFONY = '@Symfony';

    private const GROUPS = [
        self::GROUP_EXPERIMENTAL,
        self::GROUP_SONATA,
        self::GROUP_SYMFONY,
    ];

    /** @var Rule[] */
    private $rules = [];

    /** @var Rule[] */
    private $rawRules = [];

    public function __construct(iterable $rules)
    {
        foreach ($rules as $rule) {
            \assert($rule instanceof Rule);

            $this->rawRules[] = $rule;

            if ($rule instanceof CheckListRule) {
                $i = 0;
                foreach ($rule::getList() as $search => $message) {
                    $clonedRule = clone $rule;
                    $this->rules[$rule::getName()->asString().'_'.$i] = $clonedRule->configure($search, $message);
                    ++$i;
                }
                continue;
            }

            $this->rules[$rule::getName()->asString()] = $rule;
        }
    }

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
            if (\in_array($group, $rule::getGroups())) {
                $rules[] = $rule;
            }
        }

        return $rules;
    }

    public function getRule(RuleName $name): Rule
    {
        if (!isset($this->rules[$name->asString()])) {
            throw new \InvalidArgumentException(sprintf('Could not find rule:: %s', $name->asString()));
        }

        return $this->rules[$name->asString()];
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
                if (preg_match(sprintf('/%s/', $name->asString()), $key)) {
                    $rules[] = $rule;
                }
            }

            if (empty($rules)) {
                throw $e;
            }
        }

        return $rules;
    }
}
