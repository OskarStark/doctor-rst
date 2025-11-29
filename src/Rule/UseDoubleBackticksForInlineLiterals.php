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
     * Regex pattern to match single-backtick content that is NOT preceded by a role
     * and NOT followed by an underscore (RST link).
     * This pattern captures text like `word` but not :role:`word` or `link`_.
     */
    private const string PATTERN = '/(?<!:)(?<!\w)`([^`\n]+)`(?!`)(?!_)/';

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

        // Match single-backtick patterns that are not part of a role
        if (preg_match_all(self::PATTERN, $rawLine, $matches, \PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $content = $match[1];

                // Skip empty content
                if ('' === trim($content)) {
                    continue;
                }

                // Skip if it looks like it's part of a role (check for preceding colon and role name)
                $position = strpos($rawLine, '`'.$content.'`');

                if (false !== $position && 0 < $position) {
                    $before = substr($rawLine, 0, $position);

                    // Check if this is part of a role like :ref:`...` or :doc:`...`
                    if (preg_match('/:[a-z-]+$/i', $before)) {
                        continue;
                    }
                }

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
