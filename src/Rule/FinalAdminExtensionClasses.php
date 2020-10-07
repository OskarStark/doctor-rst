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

use App\Value\Lines;
use App\Value\RuleGroup;
use function Symfony\Component\String\u;

class FinalAdminExtensionClasses extends AbstractRule implements Rule
{
    public static function getGroups(): array
    {
        return [RuleGroup::Sonata()];
    }

    public function check(Lines $lines, int $number): ?string
    {
        $lines->seek($number);
        $line = $lines->current();

        if (u($line->clean())->match('/^class(.*)extends AbstractAdminExtension$/')) {
            return 'Please use "final" for AdminExtension class';
        }

        return null;
    }
}
