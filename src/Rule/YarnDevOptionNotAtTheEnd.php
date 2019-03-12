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

use App\Rst\RstParser;

class YarnDevOptionNotAtTheEnd extends AbstractRule implements Rule
{
    public function check(\ArrayIterator $lines, int $number)
    {
        $lines->seek($number);
        $line = $lines->current();

        $line = RstParser::clean($line);
        if (preg_match('/yarn add(.*)\-\-dev$/', $line)) {
            return 'Please move "--dev" option before the package';
        }
    }
}
