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
use App\Value\RuleGroup;

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

    public function check(Lines $lines, int $number): ?string
    {
        $lines->seek($number);
        $line = $lines->current();

        if ($line->isBlank() || !$line->clean()->endsWith(':')) {
            return null;
        }

        if ($line->clean()->endsWith('::')
            || RstParser::isOption($line)
            || $this->in(RstParser::DIRECTIVE_CODE_BLOCK, clone $lines, $number, [RstParser::CODE_BLOCK_YAML])
        ) {
            return null;
        }

        $lines->next();

        if (!$lines->valid()) {
            return null;
        }

        if ($lines->current()->isBlank()) {
            return null;
        }

        return sprintf(
            'Please add a blank line after "%s"',
            $line->clean()->toString()
        );
    }
}
