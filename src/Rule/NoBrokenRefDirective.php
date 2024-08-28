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
use App\Value\Lines;
use App\Value\NullViolation;
use App\Value\RuleGroup;
use App\Value\Violation;
use App\Value\ViolationInterface;

#[Description('Ensure only valid :ref: directives.')]
#[InvalidExample('See this ref:`Foo`')]
#[ValidExample('See this :ref:`Foo`')]
final class NoBrokenRefDirective extends AbstractRule implements LineContentRule
{
    public static function getGroups(): array
    {
        return [RuleGroup::Symfony()];
    }

    public function check(Lines $lines, int $number, string $filename): ViolationInterface
    {
        $lines->seek($number);
        $line = $lines->current();

        if ($line->clean()->match('/(:ref\s|ref:)/') && !$line->clean()->match('/:ref:/')) {
            return Violation::from(
                'Please use correct syntax for :ref: directive',
                $filename,
                $number + 1,
                $line,
            );
        }

        return NullViolation::create();
    }
}
