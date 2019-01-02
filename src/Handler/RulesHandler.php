<?php

declare(strict_types=1);

/*
 * This file is part of the rst-checker.
 *
 * (c) Oskar Stark <oskarstark@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Handler;

use App\Rule\Rule;

class RulesHandler
{
    /** @var Rule[] */
    private $rules;

    public function __construct(iterable $rules)
    {
        foreach ($rules as $rule) {
            $this->rules[\get_class($rule)] = $rule;
        }
    }

    public function getRules()
    {
        return $this->rules;
    }

    public function getRule($class): Rule
    {
        if (!isset($this->rules[$class])) {
            throw new \InvalidArgumentException(sprintf('Could not find rule:: %s', $class));
        }

        return $this->rules[$class];
    }
}
