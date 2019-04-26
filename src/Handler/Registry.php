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
use Webmozart\Assert\Assert;

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
                    $this->rules[$rule::getName().'_'.$i] = $clonedRule->configure($search, $message);
                    ++$i;
                }
                continue;
            }

            $this->rules[$rule::getName()] = $rule;
        }
    }

    public function setRules(array $rules): self
    {
        $this->rules = $rules;

        return $this;
    }

    public function getRules()
    {
        return $this->rules;
    }

    public function getRawRules()
    {
        return $this->rawRules;
    }

    public function getRulesByGroup(string $group)
    {
        Assert::oneOf($group, self::GROUPS);

        $rules = [];
        foreach ($this->rules as $rule) {
            if (\in_array($group, $rule::getGroups())) {
                $rules[] = $rule;
            }
        }

        return $rules;
    }

    public function getRule(string $name): Rule
    {
        if (!isset($this->rules[$name])) {
            throw new \InvalidArgumentException(sprintf('Could not find rule:: %s', $name));
        }

        return $this->rules[$name];
    }

    public function getRulesByName(string $name): array
    {
        $rules = [];

        try {
            $rules[] = $this->getRule($name);
        } catch (\InvalidArgumentException $e) {
            foreach ($this->rules as $key => $rule) {
                if (preg_match(sprintf('/%s/', $name), $key)) {
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
