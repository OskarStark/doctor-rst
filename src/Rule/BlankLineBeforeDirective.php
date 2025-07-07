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

/**
 * @no-named-arguments
 */
#[Description('Make sure you have a blank line before each directive.')]
class BlankLineBeforeDirective extends AbstractRule implements LineContentRule
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

        if (0 === $number) {
            // it is ok to start with a directive
            return NullViolation::create();
        }

        if ($line->isDefaultDirective() || !$line->isDirective()) {
            return NullViolation::create();
        }

        $lines->seek($number - 1);

        if ($lines->valid()
            && !$lines->current()->isBlank()
            && !RstParser::directiveIs($lines->current(), RstParser::DIRECTIVE_CLASS)
            && !RstParser::isComment($lines->current())
        ) {
            return Violation::from(
                \sprintf('Please add a blank line before "%s" directive', $line->raw()->toString()),
                $filename,
                $number + 1,
                $line,
            );
        }

        return NullViolation::create();
    }
}
