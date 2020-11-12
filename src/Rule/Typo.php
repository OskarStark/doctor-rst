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

namespace App\Rule;

use App\Annotations\Rule\Description;
use App\Value\Lines;
use App\Value\RuleGroup;

/**
 * @Description("Report common typos.")
 */
class Typo extends CheckListRule
{
    public static function getGroups(): array
    {
        return [
            RuleGroup::Sonata(),
            RuleGroup::Symfony(),
        ];
    }

    public function check(Lines $lines, int $number): ?string
    {
        $lines->seek($number);
        $line = $lines->current()->raw();

        if ($matches = $line->match($this->search)) {
            return sprintf($this->message, $matches[0]);
        }

        return null;
    }

    public static function getDefaultMessage(): string
    {
        return 'Typo in word "%s"';
    }

    /**
     * @return array<string, string|null>
     */
    public static function getList(): array
    {
        return [
            '/compsoer/i' => null,
            '/registerbundles\(\)/' => 'Typo in word "%s", use "registerBundles()"',
            '/retun/' => null,
            '/displayes/i' => null,
            '/mantains/i' => null,
            '/doctine/i' => null,
            '/adress/i' => null,
            '/argon21/' => 'Typo in word "%s", use "argon2i"',
            '/descritpion/i' => null,
            '/recalcuate/i' => null,
            '/achived/i' => null,
            '/overriden/i' => null,
            '/succesfully/i' => null,
            '/optionnally/i' => null,
        ];
    }
}
