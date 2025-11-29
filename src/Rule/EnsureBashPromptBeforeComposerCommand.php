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

#[Description('Make sure Composer command in a terminal/bash code block is prefixed with a $.')]
#[InvalidExample('composer require symfony/var-dumper')]
#[ValidExample('$ composer require symfony/var-dumper')]
final class EnsureBashPromptBeforeComposerCommand extends AbstractRule implements LineContentRule
{
    use DirectiveTrait;

    public static function getGroups(): array
    {
        return [RuleGroup::Symfony()];
    }

    public function check(Lines $lines, int $number, string $filename): ViolationInterface
    {
        $lines->seek($number);
        $line = $lines->current();

        if ($line->isBlank()) {
            return NullViolation::create();
        }

        $cleanLine = $line->clean();

        if (!$cleanLine->startsWith('composer')) {
            return NullViolation::create();
        }

        if (!$this->inShellCodeBlock($lines, $number)) {
            return NullViolation::create();
        }

        $content = $this->getDirectiveContent(RstParser::DIRECTIVE_CODE_BLOCK, $lines, $number);

        if ($content->numberOfLines() > 1) {
            return NullViolation::create();
        }

        return Violation::from(
            'Please add a bash prompt "$" before composer command',
            $filename,
            $number + 1,
            $line,
        );
    }
}
