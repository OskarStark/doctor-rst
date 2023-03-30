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
use App\Value\Lines;
use App\Value\NullViolation;
use App\Value\RuleGroup;
use App\Value\Violation;
use App\Value\ViolationInterface;

#[Description('Ensure no bash prompt `$` is used before commands in `bash`, `shell` or `terminal` code blocks.')]
#[InvalidExample('$ bin/console list')]
#[ValidExample('bin/console list')]
class NoBashPrompt extends AbstractRule implements LineContentRule
{
    public static function getGroups(): array
    {
        return [RuleGroup::Sonata()];
    }

    public function check(Lines $lines, int $number, string $filename): ViolationInterface
    {
        $lines->seek($number);
        $line = $lines->current();

        if (!RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_BASH)
            && !RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_SHELL)
            && !RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_TERMINAL)
        ) {
            return NullViolation::create();
        }

        $lines->next();
        $lines->next();

        $line = $lines->current();

        if ($line->clean()->match('/^\$ /')) {
            return Violation::from(
                'Please remove the "$" prefix in .. code-block:: directive',
                $filename,
                $number + 1,
                $line,
            );
        }

        return NullViolation::create();
    }
}
