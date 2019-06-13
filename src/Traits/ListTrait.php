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

namespace App\Traits;

use App\Helper\Helper;
use App\Helper\PhpHelper;
use App\Rst\RstParser;

trait ListTrait
{
    private function isPartOfListItem(\ArrayIterator $lines, int $number): bool
    {
        $lines = Helper::cloneIterator($lines, $number);

        if (RstParser::isListItem($lines->current())) {
            if ((new PhpHelper())->isPartOfMultilineComment($lines, $number)
                || (new PhpHelper())->isPartOfDocBlock($lines, $number)
            ) {
                return false;
            } else {
                return true;
            }
        }

        $currentIndention = RstParser::indention($lines->current());

        $i = $number;
        while ($i >= 1) {
            --$i;

            $lines->seek($i);

            if (RstParser::isBlankLine($lines->current())) {
                continue;
            }

            if (RstParser::isHeadline($lines->current())) {
                return false;
            }

            $lineIndention = RstParser::indention($lines->current());

            if ($lineIndention < $currentIndention
                && RstParser::isListItem($lines->current())
            ) {
                return true;
            }
        }

        return false;
    }

    private function isPartOfFootnote(\ArrayIterator $lines, int $number): bool
    {
        $lines = Helper::cloneIterator($lines, $number);

        if (RstParser::isFootnote($lines->current())) {
            return true;
        }

        $currentIndention = RstParser::indention($lines->current());

        $i = $number;
        while ($i >= 1) {
            --$i;

            $lines->seek($i);

            if (RstParser::isBlankLine($lines->current())) {
                continue;
            }

            if (RstParser::isHeadline($lines->current())) {
                return false;
            }

            $lineIndention = RstParser::indention($lines->current());

            if ($lineIndention < $currentIndention
                && RstParser::isFootnote($lines->current())
            ) {
                return true;
            }
        }

        return false;
    }

    private function isPartOfRstComment(\ArrayIterator $lines, int $number): bool
    {
        $lines = Helper::cloneIterator($lines, $number);

        if (RstParser::isComment($lines->current())) {
            return true;
        }

        $currentIndention = RstParser::indention($lines->current());

        $i = $number;
        while ($i >= 1) {
            --$i;

            $lines->seek($i);

            if (RstParser::isBlankLine($lines->current())) {
                continue;
            }

            if (RstParser::isHeadline($lines->current())) {
                return false;
            }

            $lineIndention = RstParser::indention($lines->current());

            if ($lineIndention < $currentIndention
                && RstParser::isComment($lines->current())
            ) {
                return true;
            }
        }

        return false;
    }
}
