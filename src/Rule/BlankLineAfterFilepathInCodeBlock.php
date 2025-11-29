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

namespace App\Rule;

use App\Attribute\Rule\Description;
use App\Rst\RstParser;
use App\Value\Lines;
use App\Value\NullViolation;
use App\Value\RuleGroup;
use App\Value\Violation;
use App\Value\ViolationInterface;

#[Description('Make sure you have a blank line after a filepath in a code block. This rule respects PHP, YAML, XML and Twig.')]
final class BlankLineAfterFilepathInCodeBlock extends AbstractRule implements LineContentRule
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
        ++$number;

        $lines->next();
        ++$number;

        // PHP
        if ($matches = $lines->current()->clean()->match('/^\/\/(.*)\.php$/')) {
            /** @var string[] $matches */
            return self::validateBlankLine($lines, $matches, $filename, $number);
        }

        // YML / YAML
        if ($matches = $lines->current()->clean()->match('/^#(.*)\.(yml|yaml)$/')) {
            /** @var string[] $matches */
            return self::validateBlankLine($lines, $matches, $filename, $number);
        }

        // XML
        if ($matches = $lines->current()->clean()->match('/^<!--(.*)\.xml(.*)-->$/')) {
            /** @var string[] $matches */
            return self::validateBlankLine($lines, $matches, $filename, $number);
        }

        // TWIG
        if ($matches = $lines->current()->clean()->match('/^{#(.*)\.twig(.*)#}/')) {
            /** @var string[] $matches */
            return self::validateBlankLine($lines, $matches, $filename, $number);
        }

        return NullViolation::create();
    }

    /**
     * @param string[] $matches
     */
    private static function validateBlankLine(Lines $lines, array $matches, string $filename, int $number): ViolationInterface
    {
        $lines->next();

        if ($lines->current()->isBlank()) {
            return NullViolation::create();
        }

        $match = trim((string) $matches[0]);

        return Violation::from(
            \sprintf('Please add a blank line after "%s"', $match),
            $filename,
            $number + 1,
            $match,
        );
    }
}
