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
use App\Traits\DirectiveTrait;
use App\Value\Lines;
use App\Value\NullViolation;
use App\Value\RuleGroup;
use App\Value\Violation;
use App\Value\ViolationInterface;

/**
 * @Description("Make sure you have a blank line after a sentence which ends with a colon (`:`).")
 */
class BlankLineAfterColon extends AbstractRule implements LineContentRule
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

        if ($line->isBlank() || !$line->clean()->endsWith(':')) {
            return NullViolation::create();
        }

        if ($line->clean()->endsWith('::')
            || RstParser::isOption($line)
            || $this->in(RstParser::DIRECTIVE_CODE_BLOCK, clone $lines, $number, [RstParser::CODE_BLOCK_YAML])
        ) {
            return NullViolation::create();
        }

        $lines->next();

        if (!$lines->valid() || $lines->current()->isBlank()) {
            return NullViolation::create();
        }

        return Violation::from(
            sprintf('Please add a blank line after "%s"', $line->clean()->toString()),
            $filename,
            $number + 1,
            $line
        );
    }
}
