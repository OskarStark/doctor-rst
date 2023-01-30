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
use App\Traits\DirectiveTrait;
use App\Value\Lines;
use App\Value\RuleGroup;

/**
 * @Description("Make sure you have max 2 colons (`::`).")
 *
 * @InvalidExample("composer require --dev symfony/var-dumper")
 *
 * @ValidExample("composer require symfony/var-dumper --dev")
 */
final class MaxColons extends AbstractRule implements LineContentRule
{
    use DirectiveTrait;

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
        $line = $lines->current();

        if ($line->isBlank() || !$line->clean()->endsWith(':::')) {
            return null;
        }

        return 'Please use max 2 colons at the end.';
    }
}
