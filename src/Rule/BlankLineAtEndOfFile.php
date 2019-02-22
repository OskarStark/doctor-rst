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
use App\Rst\RstParser;

class BlankLineAtEndOfFile extends AbstractRule implements Rule
{
    public static function getGroups(): array
    {
        return [Registry::GROUP_EXPERIMENTAL];
    }

    public function check(\ArrayIterator $lines, int $number)
    {
        $lines->seek($number);
        $line = $lines->current();

        if (RstParser::hasNewline($line)) {
            return;
        }

        $lines->next();

        // next line exists, no need to check here
        if ($lines->valid()) {
            return;
        }

        // next line does not exists, check for blank line
        if (!RstParser::isBlankLine($line)) {
            return 'Please add a blank line add the end of the file';
        }
    }
}
