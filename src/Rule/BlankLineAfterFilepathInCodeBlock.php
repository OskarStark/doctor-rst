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
use App\Handler\Registry;
use App\Rst\RstParser;

/**
 * @Description("Make sure you have a blank line after a filepath in a code block. This rule respects PHP, YAML, XML and Twig.")
 */
class BlankLineAfterFilepathInCodeBlock extends AbstractRule implements Rule
{
    public static function getGroups(): array
    {
        return [Registry::GROUP_SONATA];
    }

    public function check(\ArrayIterator $lines, int $number)
    {
        $lines->seek($number);
        $line = $lines->current();

        if (!RstParser::directiveIs($line, RstParser::DIRECTIVE_CODE_BLOCK)) {
            return;
        }

        $lines->next();
        $lines->next();

        // PHP
        if (preg_match('/^\/\/(.*)\.php$/', RstParser::clean($lines->current()), $matches)) {
            return $this->validateBlankLine($lines, $matches);
        }

        // YML / YAML
        if (preg_match('/^#(.*)\.(yml|yaml)$/', RstParser::clean($lines->current()), $matches)) {
            return $this->validateBlankLine($lines, $matches);
        }

        // XML
        if (preg_match('/^<!--(.*)\.xml(.*)-->$/', RstParser::clean($lines->current()), $matches)) {
            return $this->validateBlankLine($lines, $matches);
        }

        // TWIG
        if (preg_match('/^{#(.*)\.twig(.*)#}/', RstParser::clean($lines->current()), $matches)) {
            return $this->validateBlankLine($lines, $matches);
        }
    }

    private function validateBlankLine(\ArrayIterator $lines, array $matches)
    {
        $lines->next();

        if (!RstParser::isBlankLine($lines->current())) {
            return sprintf('Please add a blank line after "%s"', trim($matches[0]));
        }
    }
}
