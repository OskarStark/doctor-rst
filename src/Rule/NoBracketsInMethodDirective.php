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
use App\Annotations\Rule\InvalidExample;
use App\Annotations\Rule\ValidExample;
use App\Value\Lines;
use App\Value\RuleGroup;

/**
 * @Description("Ensure a :method: directive has special format.")
 * @InvalidExample(":method:`Symfony\\Component\\OptionsResolver\\Options::offsetGet()`")
 * @ValidExample(":method:`Symfony\\Component\\OptionsResolver\\Options::offsetGet`")
 */
class NoBracketsInMethodDirective extends AbstractRule implements LineContentRule
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

        if ($lines->current()->raw()->match('/:method:`.*::.*\(\)`/')) {
            return 'Please remove "()" inside :method: directive';
        }

        return null;
    }
}
