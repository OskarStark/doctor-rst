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
use App\Value\NullViolation;
use App\Value\RuleGroup;
use App\Value\Violation;
use App\Value\ViolationInterface;

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

    public function check(Lines $lines, int $number, string $filename): ViolationInterface
    {
        $lines->seek($number);
        $line = $lines->current();

        if (!RstParser::directiveIs($line, RstParser::DIRECTIVE_CODE_BLOCK)) {
            return NullViolation::create();
        }

        $lines->next();
        $lines->next();

        // PHP
        if ($matches = $lines->current()->clean()->match('/^\/\/(.*)\.php$/')) {
            return $this->validateBlankLine($lines, $matches, $filename);
        }

        // YML / YAML
        if ($matches = $lines->current()->clean()->match('/^#(.*)\.(yml|yaml)$/')) {
            return $this->validateBlankLine($lines, $matches, $filename);
        }

        // XML
        if ($matches = $lines->current()->clean()->match('/^<!--(.*)\.xml(.*)-->$/')) {
            return $this->validateBlankLine($lines, $matches, $filename);
        }

        // TWIG
        if ($matches = $lines->current()->clean()->match('/^{#(.*)\.twig(.*)#}/')) {
            return $this->validateBlankLine($lines, $matches, $filename);
        }

        return NullViolation::create();
    }

    private function validateBlankLine(Lines $lines, array $matches, string $filename): ViolationInterface
    {
        $lines->next();

        if (!$lines->current()->isBlank()) {
            $message = sprintf('Please add a blank line after "%s"', trim($matches[0]));

            return Violation::from(
                $message,
                $filename,
                1,
                ''
            );
        }

        return NullViolation::create();
    }
}
