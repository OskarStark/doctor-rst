<?php

declare(strict_types=1);

/**
 * This file is part of DOCtor-RST.
 *
 * (c) Oskar Stark <oskarstark@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Traits;

use App\Rst\RstParser;
use App\Value\Lines;

trait TableTrait
{
    /**
     * The columns of a simple table are aligned under its borders, so its cells and the
     * continuation lines of those cells follow the width of the columns rather than the
     * indention of the document.
     */
    private function isPartOfSimpleTable(Lines $lines, int $number): bool
    {
        return $this->hasTableBorderAbove($lines, $number) && $this->hasTableBorderBelow($lines, $number);
    }

    private function hasTableBorderAbove(Lines $lines, int $number): bool
    {
        for ($i = $number - 1; 0 <= $i; --$i) {
            $lines->seek($i);

            if ($lines->current()->isBlank()) {
                return false;
            }

            if (RstParser::isTable($lines->current())) {
                return true;
            }
        }

        return false;
    }

    private function hasTableBorderBelow(Lines $lines, int $number): bool
    {
        $lines->seek($number);
        $lines->next();

        while ($lines->valid()) {
            if ($lines->current()->isBlank()) {
                return false;
            }

            if (RstParser::isTable($lines->current())) {
                return true;
            }

            $lines->next();
        }

        return false;
    }
}
