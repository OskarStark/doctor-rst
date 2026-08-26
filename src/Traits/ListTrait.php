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

use App\Helper\PhpHelper;
use App\Rst\RstParser;
use App\Value\Lines;

trait ListTrait
{
    private function isPartOfListItem(Lines $lines, int $number): bool
    {
        $lines->seek($number);

        if (RstParser::isListItem($lines->current())) {
            return !(new PhpHelper())->isPartOfMultilineComment($lines, $number) && !(new PhpHelper())->isPartOfDocBlock($lines, $number);
        }

        $currentIndention = $lines->current()->indention();

        $i = $number;

        while (1 <= $i) {
            --$i;

            $lines->seek($i);
            $currentLine = $lines->current();

            if ($currentLine->isBlank()) {
                continue;
            }

            if ($currentLine->isHeadline()) {
                return false;
            }

            $lineIndention = $currentLine->indention();

            if ($lineIndention < $currentIndention
                && RstParser::isListItem($lines->current())
            ) {
                return true;
            }
        }

        return false;
    }

    private function isPartOfFootnote(Lines $lines, int $number): bool
    {
        $lines->seek($number);

        if (RstParser::isFootnote($lines->current())) {
            return true;
        }

        $currentIndention = $lines->current()->indention();

        $i = $number;

        while (1 <= $i) {
            --$i;

            $lines->seek($i);
            $currentLine = $lines->current();

            if ($currentLine->isBlank()) {
                continue;
            }

            if ($currentLine->isHeadline()) {
                return false;
            }

            $lineIndention = $currentLine->indention();

            if ($lineIndention < $currentIndention
                && RstParser::isFootnote($currentLine)
            ) {
                return true;
            }
        }

        return false;
    }

    private function isPartOfRstComment(Lines $lines, int $number): bool
    {
        $lines->seek($number);

        if (RstParser::isComment($lines->current())) {
            return true;
        }

        $currentIndention = $lines->current()->indention();

        $i = $number;

        while (1 <= $i) {
            --$i;

            $lines->seek($i);

            if ($lines->current()->isBlank()) {
                continue;
            }

            if ($lines->current()->isHeadline()) {
                return false;
            }

            $lineIndention = $lines->current()->indention();

            if ($lineIndention < $currentIndention
                && RstParser::isComment($lines->current())
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Something like:.
     *
     * Line 12
     *   You can see x here.
     *
     * Line 13 - 15
     *   You can see y here.
     */
    private function isPartOfLineNumberAnnotation(Lines $lines, int $number): bool
    {
        $lines->seek($number);

        if (RstParser::isLineNumberAnnotation($lines->current())) {
            return true;
        }

        $currentIndention = $lines->current()->indention();

        $i = $number;

        while (1 <= $i) {
            --$i;

            $lines->seek($i);

            if ($lines->current()->isBlank()) {
                continue;
            }

            if ($lines->current()->isHeadline()) {
                return false;
            }

            $lineIndention = $lines->current()->indention();

            if ($lineIndention < $currentIndention
                && RstParser::isLineNumberAnnotation($lines->current())
            ) {
                return true;
            }
        }

        return false;
    }
}
