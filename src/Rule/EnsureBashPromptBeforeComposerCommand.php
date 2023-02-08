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
use App\Annotations\Rule\InvalidExample;
use App\Annotations\Rule\ValidExample;
use App\Traits\DirectiveTrait;
use App\Value\Lines;
use App\Value\NullViolation;
use App\Value\RuleGroup;
use App\Value\Violation;
use App\Value\ViolationInterface;

/**
 * @Description("Make sure Composer command in a terminal/bash code block is prefixed with a $.")
 *
 * @InvalidExample("composer require symfony/var-dumper")
 *
 * @ValidExample("$ composer require symfony/var-dumper")
 */
class EnsureBashPromptBeforeComposerCommand extends AbstractRule implements LineContentRule
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

        $indentation = $line->indention();

        $lines->next();
        $lines->next();

        if ($indentation === $lines->current()->indention()) {
            return NullViolation::create();
        }

        $cleanLine = $line->clean();
        if ($cleanLine->startsWith('composer')
            && $this->inShellCodeBlock($lines, $number)
        ) {
            return Violation::from(
                'Please add a bash prompt "$" before composer command',
                $filename,
                $number + 1,
                $line
            );
        }

        return NullViolation::create();
    }
}
