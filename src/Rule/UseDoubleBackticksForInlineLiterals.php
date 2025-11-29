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
use App\Attribute\Rule\InvalidExample;
use App\Attribute\Rule\ValidExample;
use App\Rst\RstParser;
use App\Traits\DirectiveTrait;
use App\Value\Lines;
use App\Value\NullViolation;
use App\Value\RuleGroup;
use App\Value\Violation;
use App\Value\ViolationInterface;

/**
 * @no-named-arguments
 */
#[Description('Ensure double backticks are used for inline literals instead of single backticks.')]
#[ValidExample('Please use ``vector`` for this.')]
#[ValidExample('See :ref:`my-reference` for details.')]
#[InvalidExample('Please use `vector` for this.')]
final class UseDoubleBackticksForInlineLiterals extends AbstractRule implements LineContentRule
{
    use DirectiveTrait;

    /**
     * Regex pattern to match single-backtick inline literals that should use double backticks.
     *
     * Matches `content` where:
     * - Not preceded by :rolename (RST role like :ref:, :doc:, etc.)
     * - Content starts and ends with non-whitespace (valid inline literal format)
     * - Not followed by _ (RST link reference)
     *
     * The pattern ([^\s`][^`]*[^\s`]|\S) matches either:
     * - Multi-char content: starts non-whitespace, any middle chars, ends non-whitespace
     * - Single char: just one non-whitespace character
     */
    private const string PATTERN = '/(?<!:)(?<![a-z])`([^\s`][^`]*[^\s`]|\S)`(?!_)/i';

    public static function getGroups(): array
    {
        return [
            RuleGroup::Symfony(),
        ];
    }

    public function check(Lines $lines, int $number, string $filename): ViolationInterface
    {
        $lines->seek($number);
        $line = $lines->current();

        $rawLine = $line->raw()->toString();

        // Skip if line contains double backticks (already correct)
        // or if there's no single backtick at all
        if (!str_contains($rawLine, '`') || str_contains($rawLine, '``')) {
            return NullViolation::create();
        }

        // Skip if line is inside a code block
        if ($this->in(RstParser::DIRECTIVE_CODE_BLOCK, $lines, $number)) {
            return NullViolation::create();
        }

        // Match single-backtick patterns that are not part of a role or RST link
        if (preg_match_all(self::PATTERN, $rawLine, $matches, \PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $content = $match[1];

                return Violation::from(
                    \sprintf('Please use double backticks for inline literals: `%s` should be ``%s``', $content, $content),
                    $filename,
                    $number + 1,
                    $line,
                );
            }
        }

        return NullViolation::create();
    }
}
