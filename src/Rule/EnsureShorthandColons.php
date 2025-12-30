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

#[Description('Ensure lines end with `::` (shorthand) if followed by a blank line and an indented block.')]
#[InvalidExample(<<<'RST'
use it as follows:

    $uuidFactory = new UuidFactory();
RST)]
#[ValidExample(<<<'RST'
use it as follows::

    $uuidFactory = new UuidFactory();
RST)]
final class EnsureShorthandColons extends AbstractRule implements LineContentRule
{
    use DirectiveTrait;

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

        // Skip blank lines
        if ($line->isBlank()) {
            return NullViolation::create();
        }

        // Skip if the line ends with ::
        if ($line->clean()->endsWith('::')) {
            return NullViolation::create();
        }

        // Only check lines ending with a single :
        if (!$line->clean()->endsWith(':')) {
            return NullViolation::create();
        }

        // Skip directive options like :linenos:
        if (RstParser::isOption($line)) {
            return NullViolation::create();
        }

        // Skip list items
        if (RstParser::isListItem($line)) {
            return NullViolation::create();
        }

        // Skip if inside a code block (YAML, etc.)
        if ($this->in(RstParser::DIRECTIVE_CODE_BLOCK, clone $lines, $number)) {
            return NullViolation::create();
        }

        // Skip RST anchors like ".. _anchor:"
        if (RstParser::isAnchor($line)) {
            return NullViolation::create();
        }

        // Skip link definitions
        if (RstParser::isLinkDefinition($line)) {
            return NullViolation::create();
        }

        // Skip directives
        if ($line->isDirective()) {
            return NullViolation::create();
        }

        // Look for blank line followed by indented content
        $lines->next();

        if (!$lines->valid()) {
            return NullViolation::create();
        }

        $nextLine = $lines->current();

        // Must be followed by a blank line
        if (!$nextLine->isBlank()) {
            return NullViolation::create();
        }

        // Skip all blank lines to find the next content
        while ($lines->valid() && $lines->current()->isBlank()) {
            $lines->next();
        }

        if (!$lines->valid()) {
            return NullViolation::create();
        }

        $contentLine = $lines->current();

        // If the next content is indented, we likely have a missing ::
        if ($contentLine->indention() > $line->indention() && !$contentLine->isDirective()) {
            return Violation::from(
                'Please use "::" (shorthand) to introduce a code block.',
                $filename,
                $number + 1,
                $line,
            );
        }

        return NullViolation::create();
    }
}
