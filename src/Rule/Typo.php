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

use App\Handler\RulesHandler;

class Typo implements Rule
{
    public static function getName(): string
    {
        return 'typo';
    }

    public static function getGroups(): array
    {
        return [RulesHandler::GROUP_SONATA, RulesHandler::GROUP_SYMFONY];
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
