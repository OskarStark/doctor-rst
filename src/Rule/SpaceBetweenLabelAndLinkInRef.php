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

use function Symfony\Component\String\u;

/**
 * @Description("Ensure a space between label and link in :ref: directive.")
 *
 * @InvalidExample(":ref:`receiving them via a worker<messenger-worker>`")
 *
 * @ValidExample(":ref:`receiving them via a worker <messenger-worker>`")
 */
class SpaceBetweenLabelAndLinkInRef extends AbstractRule implements LineContentRule
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

        if ($matches = $line->match('/:ref:`(?P<label>.*)<(?P<link>.*)>`/')) {
            if (!u($matches['label'])->endsWith(' ')) {
                return sprintf(
                    'Please add a space between "%s" and "<%s>" inside :ref: directive',
                    $matches['label'],
                    $matches['link']
                );
            }
        }

        return null;
    }
}
