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
use App\Value\Lines;

class NoBlankLineAfterFilepathInCodeBlock extends AbstractRule implements Rule
{
    public function check(Lines $lines, int $number): ?string
    {
        $lines->seek($number);
        $line = $lines->current();

        if (!RstParser::directiveIs($line, RstParser::DIRECTIVE_CODE_BLOCK)) {
            return null;
        }

        $lines->next();
        $lines->next();

        // PHP
        if (preg_match('/^\/\/(.*)\.php$/', $lines->current()->clean(), $matches)) {
            return $this->validateBlankLine($lines, $matches);
        }

        // YML / YAML
        if (preg_match('/^#(.*)\.(yml|yaml)$/', $lines->current()->clean(), $matches)) {
            return $this->validateBlankLine($lines, $matches);
        }

        // XML
        if (preg_match('/^<!--(.*)\.xml(.*)-->$/', $lines->current()->clean(), $matches)) {
            return $this->validateBlankLine($lines, $matches);
        }

        // TWIG
        if (preg_match('/^{#(.*)\.twig(.*)#}/', $lines->current()->clean(), $matches)) {
            return $this->validateBlankLine($lines, $matches);
        }

        return null;
    }

    private function validateBlankLine(Lines $lines, array $matches): ?string
    {
        $lines->next();

        if ($lines->current()->isBlank()) {
            return sprintf('Please remove blank line after "%s"', trim($matches[0]));
        }

        return null;
    }
}
