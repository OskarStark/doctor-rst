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
 * @Description("Make sure yarn `--dev` option for `add` command is used at the end.")
 *
 * @InvalidExample("yarn add --dev jquery")
 *
 * @ValidExample("yarn add jquery --dev")
 */
class YarnDevOptionAtTheEnd extends AbstractRule implements LineContentRule
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
        $line = $lines->current();

        if ($line->clean()->match('/yarn add \-\-dev(.*)$/')) {
            return 'Please move "--dev" option to the end of the command';
        }

        return null;
    }
}
