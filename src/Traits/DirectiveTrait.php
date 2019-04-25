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

use App\Rst\RstParser;

trait DirectiveTrait
{
    use CloneIteratorTrait;

    private function in(string $directive, \ArrayIterator $lines, int $number, array $directiveTypes = null): bool
    {
        $lines = $this->cloneIterator($lines, $number);

        $currentIndention = RstParser::indention($lines->current());

        $i = $number;
        while ($i >= 1) {
            --$i;

            $lines->seek($i);

            if (RstParser::isBlankLine($lines->current())) {
                continue;
            }

            $lineIndention = RstParser::indention($lines->current());

            if ($lineIndention < $currentIndention
                && RstParser::isDirective($lines->current())
            ) {
                if (RstParser::directiveIs($lines->current(), $directive)) {
                    if (null !== $directiveTypes) {
                        $found = false;
                        foreach ($directiveTypes as $type) {
                            if (RstParser::codeBlockDirectiveIsTypeOf($lines->current(), $type)) {
                                $found = true;
                                break;
                            }
                        }

                        return $found;
                    }

                    return true;
                }

                return false;
            }
        }

        return false;
    }
}
