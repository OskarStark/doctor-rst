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

class PhpPrefixBeforeBinConsole extends AbstractRule implements Rule
{
    public static function getGroups(): array
    {
        return [RulesHandler::GROUP_SYMFONY];
    }

    public function check(\ArrayIterator $lines, int $number)
    {
        $lines->seek($number);
        $line = $lines->current();

        if (!preg_match('/bin\/console/', $line)) {
            return;
        }

        if (preg_match('/(`|"|_)bin\/console/', $line)
            || preg_match('/php "%s\/\.\.\/bin\/console"/', $line)) {
            return;
        }

        if (!preg_match('/php(.*)bin\/console/', $line)) {
            return 'Please add "php" prefix before "bin/console"';
        }
    }
}
