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

#[Description('Ensure a directive is not empty.')]
#[InvalidExample(<<<'RST'
.. note::

This is a note.
RST)]
#[ValidExample(<<<'RST'
.. note::

    This is a note.
RST)]
final class NoEmptyDirective extends AbstractRule implements LineContentRule
{
    use DirectiveTrait;

    /**
     * Directives that require content body.
     */
    private const array DIRECTIVES_REQUIRING_CONTENT = [
        RstParser::DIRECTIVE_NOTE,
        RstParser::DIRECTIVE_WARNING,
        RstParser::DIRECTIVE_CAUTION,
        RstParser::DIRECTIVE_TIP,
        RstParser::DIRECTIVE_IMPORTANT,
        RstParser::DIRECTIVE_SEEALSO,
        RstParser::DIRECTIVE_BEST_PRACTICE,
        RstParser::DIRECTIVE_ADMONITION,
        RstParser::DIRECTIVE_NOTICE,
    ];

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

        $directive = self::getMatchingDirective($line->clean()->toString());

        if (null === $directive) {
            return NullViolation::create();
        }

        try {
            $content = $this->getDirectiveContent($directive, $lines, $number);
            $isEmpty = 0 === $content->numberOfLines();
        } catch (\OutOfBoundsException) {
            // Directive at end of file with no content
            $isEmpty = true;
        }

        if ($isEmpty) {
            return Violation::from(
                \sprintf('The "%s" directive must not be empty.', $directive),
                $filename,
                $number + 1,
                $line,
            );
        }

        return NullViolation::create();
    }

    private static function getMatchingDirective(string $lineContent): ?string
    {
        foreach (self::DIRECTIVES_REQUIRING_CONTENT as $directive) {
            if (str_contains($lineContent, $directive)) {
                return $directive;
            }
        }

        return null;
    }
}
