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

use App\Annotations\Rule\Description;
use App\Rst\RstParser;
use App\Value\Lines;
use App\Value\RuleGroup;

/**
 * @Description("Make sure you have a blank line after a filepath in a code block. This rule respects PHP, YAML, XML and Twig.")
 */
class BlankLineAfterFilepathInCodeBlock extends AbstractRule implements LineContentRule
{
    public static function getGroups(): array
    {
        return [
            RuleGroup::Sonata(),
            RuleGroup::Symfony(),
        ];
    }

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
        if ($matches = $lines->current()->clean()->match('/^\/\/(.*)\.php$/')) {
            return $this->validateBlankLine($lines, $matches);
        }

        // YML / YAML
        if ($matches = $lines->current()->clean()->match('/^#(.*)\.(yml|yaml)$/')) {
            return $this->validateBlankLine($lines, $matches);
        }

        // XML
        if ($matches = $lines->current()->clean()->match('/^<!--(.*)\.xml(.*)-->$/')) {
            return $this->validateBlankLine($lines, $matches);
        }

        // TWIG
        if ($matches = $lines->current()->clean()->match('/^{#(.*)\.twig(.*)#}/')) {
            return $this->validateBlankLine($lines, $matches);
        }

        return null;
    }

    private function validateBlankLine(Lines $lines, array $matches): ?string
    {
        $lines->next();

        if (!$lines->current()->isBlank()) {
            return sprintf('Please add a blank line after "%s"', trim($matches[0]));
        }

        return null;
    }
}
