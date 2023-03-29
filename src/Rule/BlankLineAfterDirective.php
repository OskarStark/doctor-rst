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

#[Description('Make sure you have a blank line after each directive.')]
class BlankLineAfterDirective extends AbstractRule implements LineContentRule
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

        if (!$line->isDirective()) {
            return NullViolation::create();
        }

        foreach (self::unSupportedDirectives() as $type) {
            if (RstParser::directiveIs($line, $type)) {
                return NullViolation::create();
            }
        }

        $lines->next();

        while ($lines->valid() && RstParser::isOption($lines->current())) {
            $lines->next();
        }

        if (!$lines->valid() || !$lines->current()->isBlank()) {
            $message = sprintf('Please add a blank line after "%s" directive', $line->raw()->toString());

            return Violation::from(
                $message,
                $filename,
                $number + 1,
                $line,
            );
        }

        return NullViolation::create();
    }

    /**
     * @return array<int, string>
     */
    public static function unSupportedDirectives(): array
    {
        return [
            RstParser::DIRECTIVE_INDEX,
            RstParser::DIRECTIVE_TOCTREE,
            RstParser::DIRECTIVE_INCLUDE,
            RstParser::DIRECTIVE_IMAGE,
            RstParser::DIRECTIVE_ADMONITION,
            RstParser::DIRECTIVE_ROLE,
            RstParser::DIRECTIVE_FIGURE,
            RstParser::DIRECTIVE_CLASS,
            RstParser::DIRECTIVE_RST_CLASS,
            RstParser::DIRECTIVE_CONTENTS,
            RstParser::DIRECTIVE_CODEIMPORT,
        ];
    }
}
