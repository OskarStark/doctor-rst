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

#[Description('Ensure explicit nullable types in method arguments.')]
#[ValidExample('function foo(?string $bar = null)')]
#[ValidExample('function foo(string|null $bar = null)')]
#[InvalidExample('function foo(string $bar = null)')]
final class EnsureExplicitNullableTypes extends AbstractRule implements LineContentRule
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

        if (!RstParser::isPhpDirective($line)) {
            return NullViolation::create();
        }

        $lines->next();

        while ($lines->valid() && !$lines->current()->isDirective()) {
            ++$number;

            if (!str_contains((string) $lines->current()->clean(), ' = null')) {
                $lines->next();

                continue;
            }

            $pattern = '#([\w|\\\\?]+)\s+\$(\w+)\s*=\s*null[\n,\)]#';

            if ($matches = $lines->current()->clean()->match($pattern, \PREG_SET_ORDER)) {
                /** @var string[] $matches */
                foreach ($matches as $match) {
                    $types = $match[1];

                    // ?int $id = null
                    if (str_starts_with($types, '?')) {
                        continue;
                    }

                    // mixed $id = null
                    if ('mixed' === $types) {
                        continue;
                    }

                    // int|string|null $id = null
                    $types = explode('|', $types);

                    if (\in_array('null', $types, true)) {
                        continue;
                    }

                    return Violation::from(
                        'Please use explicit nullable types.',
                        $filename,
                        $number + 1,
                        $lines->current()->clean()->toString(),
                    );
                }
            }

            $lines->next();
        }

        return NullViolation::create();
    }
}
