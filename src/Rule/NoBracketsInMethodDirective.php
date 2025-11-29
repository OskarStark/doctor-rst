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

/**
 * @no-named-arguments
 */
#[Description('Ensure a :method: directive has special format.')]
#[InvalidExample(':method:`Symfony\\\\Component\\\\OptionsResolver\\\\Options::offsetGet()`')]
#[ValidExample(':method:`Symfony\\\\Component\\\\OptionsResolver\\\\Options::offsetGet`')]
final class NoBracketsInMethodDirective extends AbstractRule implements LineContentRule
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

        if ($line->raw()->match('/:method:`.*::.*\(\)`/')) {
            return Violation::from(
                'Please remove "()" inside :method: directive',
                $filename,
                $number + 1,
                $line,
            );
        }

        return NullViolation::create();
    }
}
