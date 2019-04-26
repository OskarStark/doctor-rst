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

use App\Handler\Registry;

class NoAppConsole extends AbstractRule implements Rule
{
    public static function getGroups(): array
    {
        return [Registry::GROUP_SONATA, Registry::GROUP_SYMFONY];
    }

    public function check(\ArrayIterator $lines, int $number)
    {
        $lines->seek($number);
        $line = $lines->current();

        if (preg_match('/app\/console/', $line)) {
            return 'Please use "bin/console" instead of "app/console"';
        }
    }
}
