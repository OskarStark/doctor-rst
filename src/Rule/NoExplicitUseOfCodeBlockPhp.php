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

use App\Rst\RstParser;
use App\Traits\DirectiveTrait;
use App\Value\Lines;
use App\Value\NullViolation;
use App\Value\RuleGroup;
use App\Value\Violation;
use App\Value\ViolationInterface;

final class NoExplicitUseOfCodeBlockPhp extends AbstractRule implements LineContentRule
{
    use DirectiveTrait;

    /**
     * @var string[]
     */
    final public const array ALLOWED_PREVIOUS_DIRECTIVES = [
        RstParser::DIRECTIVE_CAUTION,
        RstParser::DIRECTIVE_CONFIGURATION_BLOCK,
        RstParser::DIRECTIVE_DEPRECATED,
        RstParser::DIRECTIVE_NOTE,
        RstParser::DIRECTIVE_NOTICE,
        RstParser::DIRECTIVE_SEEALSO,
        RstParser::DIRECTIVE_VERSIONADDED,
        RstParser::DIRECTIVE_VERSIONCHANGED,
        RstParser::DIRECTIVE_WARNING,
        RstParser::DIRECTIVE_IMAGE,
    ];

    public static function getGroups(): array
    {
        return [RuleGroup::Symfony()];
    }

    public function check(Lines $lines, int $number, string $filename): ViolationInterface
    {
        $lines->seek($number);
        $line = $lines->current();

        // only interesting if a PHP code block
        if (!RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_PHP, true)) {
            return NullViolation::create();
        }

        // :: is a php code block, but its ok
        if (preg_match('/\:\:$/', $line->clean()->toString())) {
            return NullViolation::create();
        }

        // it has no indention, check if it comes after a headline, in this case its ok
        if (!preg_match('/^[\s]+/', $line->raw()->toString(), $matches)) {
            if (self::directAfterHeadline($lines, $number)
                || self::directAfterTable($lines, $number)
                || self::previousParagraphEndsWithQuestionMark($lines, $number)
            ) {
                return NullViolation::create();
            }
        }

        // check if the code block is not on the first level, in this case
        // it could not be in a configuration block which would be ok
        if (preg_match('/^[\s]+/', $line->raw()->toString(), $matches)
            && RstParser::codeBlockDirectiveIsTypeOf($lines->current(), RstParser::CODE_BLOCK_PHP)
            && 0 < $number
        ) {
            if ($this->in(RstParser::DIRECTIVE_CONFIGURATION_BLOCK, $lines, $number)) {
                return NullViolation::create();
            }

            if ($this->in(RstParser::DIRECTIVE_CODE_BLOCK, $lines, $number, [RstParser::CODE_BLOCK_TEXT, RstParser::CODE_BLOCK_RST])) {
                return NullViolation::create();
            }
        }

        $previousAllowedDirectiveTypes = [
            RstParser::CODE_BLOCK_PHP,
            RstParser::CODE_BLOCK_YAML,
            RstParser::CODE_BLOCK_TERMINAL,
        ];

        // check if the previous code block is php, yaml or terminal code block
        if ($this->previousDirectiveIs(RstParser::DIRECTIVE_CODE_BLOCK, $lines, $number, $previousAllowedDirectiveTypes)) {
            return NullViolation::create();
        }

        foreach (self::ALLOWED_PREVIOUS_DIRECTIVES as $previousDirective) {
            // check if the previous directive is ...
            if ($this->previousDirectiveIs($previousDirective, $lines, $number)) {
                return NullViolation::create();
            }
        }

        $lines->next();

        if ($lines->valid() && RstParser::isOption($lines->current())) {
            return NullViolation::create();
        }

        return Violation::from(
            'Please do not use ".. code-block:: php", use "::" instead.',
            $filename,
            $number + 1,
            $line,
        );
    }

    private static function directAfterHeadline(Lines $lines, int $number): bool
    {
        $lines->seek($number);

        $i = $number;

        while (1 <= $i) {
            --$i;

            $lines->seek($i);

            if ($lines->current()->isBlank()) {
                continue;
            }

            return $lines->current()->isHeadline();
        }

        return false;
    }

    private static function directAfterTable(Lines $lines, int $number): bool
    {
        $lines->seek($number);

        $i = $number;

        while (1 <= $i) {
            --$i;

            $lines->seek($i);

            if ($lines->current()->isBlank()) {
                continue;
            }

            return RstParser::isTable($lines->current());
        }

        return false;
    }

    private static function previousParagraphEndsWithQuestionMark(Lines $lines, int $number): bool
    {
        $lines->seek($number);

        $i = $number;

        while (1 <= $i) {
            --$i;

            $lines->seek($i);

            if ($lines->current()->isBlank()) {
                continue;
            }

            return (bool) preg_match('/\?$/', $lines->current()->clean()->toString());
        }

        return false;
    }
}
