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

namespace App\Rule\Sonata;

use App\Rule\Rule;

class FinalAdminClasses implements Rule
{
    public function check(\ArrayIterator $lines, int $number)
    {
        return;

        $lines->seek($number);
        $line = $lines->current();

        if (strstr($line, 'extends AbstractAdminExtension') && !strstr($line, 'final')) {
            return 'Please use "final" for AdminExtension class';
        }

        if (strstr($line, 'extends AbstractAdmin') && !strstr($line, 'final')) {
            return 'Please use "final" for Admin class';
        }
    }
}
