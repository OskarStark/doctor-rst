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
use App\Value\Lines;

trait DirectiveTrait
{
    private function in(string $directive, Lines $lines, int $number, array $directiveTypes = null): bool
    {
        $lines->seek($number);

        $currentIndention = $lines->current()->indention();

        $i = $number;
        while ($i >= 1) {
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
                && $lines->current()->isDirective()
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

    private function previousDirectiveIs(string $directive, Lines $lines, int $number, array $directiveTypes = null): bool
    {
        $lines->seek($number);

        $initialIndention = $lines->current()->indention();

        $i = $number;
        while ($i >= 1) {
            --$i;

            $lines->seek($i);

            if ($lines->current()->isBlank()) {
                continue;
            }

            $lineIndention = $lines->current()->indention();

            if ($lineIndention < $initialIndention) {
                return false;
            }

            if ($lineIndention === $initialIndention && !$lines->current()->isDirective()) {
                return false;
            }

            if ((
                    $lineIndention === $initialIndention
                    && $lines->current()->isDirective()
                    && RstParser::directiveIs($lines->current(), $directive)
                ) || (0 === $lineIndention
                    && (
                        RstParser::codeBlockDirectiveIsTypeOf($lines->current(), RstParser::CODE_BLOCK_PHP)
                        || RstParser::directiveIs($lines->current(), $directive)
                    ))
            ) {
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
        }

        return false;
    }
}
