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
use App\Value\Lines;
use App\Value\NullViolation;
use App\Value\RuleGroup;
use App\Value\Violation;
use App\Value\ViolationInterface;

#[Description('Ensure explicit nullable types in method arguments.')]
class EnsureExplicitNullableTypes extends AbstractRule implements LineContentRule
{
    public static function getGroups(): array
    {
        return [
            RuleGroup::Symfony(),
        ];
    }

    public function check(Lines $lines, int $number, string $filename): ViolationInterface
    {
        $lines->seek($number);
        $line = $lines->current();

        if (!str_contains((string) $line->clean(), ' = null')) {
            return NullViolation::create();
        }

        $pattern = '/([?]?\w+)\s+\$(\w+)\s*=\s*null(?=\s*[,\)])/';

        if ($matches = $line->clean()->match($pattern)) {
            $types = $matches[1];

            // ?int $id = null
            if (str_starts_with($types, '?')) {
                return NullViolation::create();
            }

            // int|null $id = null
            $types = explode('|', $types);
            if (\in_array('null', $types, true)) {
                return NullViolation::create();
            }

            return Violation::from(
                'Please use explicit nullable types.',
                $filename,
                $number + 1,
                $line,
            );
        }

        return NullViolation::create();
    }
}
