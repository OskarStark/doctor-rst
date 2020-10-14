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
 * @Description("Ensure a space between label and link in :doc: directive.")
 * @InvalidExample(":doc:`File</reference/constraints/File>`")
 * @ValidExample(":doc:`File </reference/constraints/File>`")
 */
class SpaceBetweenLabelAndLinkInDoc extends AbstractRule implements Rule
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
        $line = $lines->current()->rawU();

        if ($matches = $line->match('/:doc:`(?P<label>.*)<(?P<link>.*)>`/')) {
            if (!u($matches['label'])->endsWith(' ')) {
                return sprintf(
                    'Please add a space between "%s" and "<%s>" inside :doc: directive',
                    $matches['label'],
                    $matches['link']
                );
            }
        }

        return null;
    }
}
