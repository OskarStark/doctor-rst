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

class RulesHandler
{
    const GROUP_DEV = '@Dev';
    const GROUP_SONATA = '@Sonata';
    const GROUP_SYMFONY = '@Symfony';

    private const GROUPS = [
        self::GROUP_DEV,
        self::GROUP_SONATA,
        self::GROUP_SYMFONY,
    ];

    /** @var Rule[] */
    private $rules = [];

    public function __construct(iterable $rules)
    {
        $this->rules = [];

        foreach ($rules as $rule) {
            \assert($rule instanceof Rule);

            if ($rule instanceof CheckListRule) {
                foreach ($rule::getList() as $suffix => $config) {
                    $clonedRule = clone $rule;

                    list($search, $message) = $config;
                    $this->rules[$rule::getName().'_'.$suffix] = $clonedRule->configure($search, $message);
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

    public function getRulesByName(string $name): iterable
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
