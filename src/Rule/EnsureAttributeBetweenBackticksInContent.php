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
use App\Value\Violation;
use App\Value\ViolationInterface;

#[Description('Make sure to use backticks around attributes in content')]
#[InvalidExample('Use #[Route] to define route')]
#[ValidExample('Use ``#[Route]`` to define route')]
final class EnsureAttributeBetweenBackticksInContent extends AbstractRule implements LineContentRule
{
    use DirectiveTrait;

    public function check(Lines $lines, int $number, string $filename): ViolationInterface
    {
        $lines->seek($number);
        $line = $lines->current();

        if ($this->inPhpCodeBlock($lines, $number)) {
            return NullViolation::create();
        }

        if ($this->in(RstParser::DIRECTIVE_CODE_BLOCK, $lines, $number, [RstParser::CODE_BLOCK_DIFF])) {
            return NullViolation::create();
        }

        if ($line->raw()->match('/(?<!`)#\[[^\]]*\](?!`)/')) {
            // Skip if the attribute is inside a :ref: directive where backticks cannot be used
            if ($line->raw()->match('/:ref:`[^`]*#\[[^\]]*\][^`]*`/')) {
                return NullViolation::create();
            }

            return Violation::from(
                \sprintf('Please ensure to use backticks "%s"', $line->raw()->toString()),
                $filename,
                $number + 1,
                $line,
            );
        }

        return NullViolation::create();
    }
}
