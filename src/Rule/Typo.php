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

namespace App\Rule;

class Typo implements Rule
{
    public static function getName(): string
    {
        return 'typo';
    }

    public function check(\ArrayIterator $lines, int $number)
    {
        $lines->seek($number);
        $line = $lines->current();

        if (strstr($line, $typo = 'compsoer')) {
            return sprintf('Typo in word "%s"', $typo);
        }

        if (strstr($line, $typo = 'registerbundles()')) {
            return sprintf('Typo in word "%s", use "registerBundles()"', $typo);
        }
    }
}
