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

class LineLength extends AbstractRule implements Rule
{
    private $max;

    public function __construct(int $max = 80)
    {
        $this->max = $max;
    }

    public function check(\ArrayIterator $lines, int $number)
    {
        $lines->seek($number);
        $line = $lines->current();

        $count = mb_strlen(RstParser::clean($line));

        if ($count > $this->max) {
            return sprintf('Line is to long (max %s) currently: %s', $this->max, $count);
        }
    }
}
