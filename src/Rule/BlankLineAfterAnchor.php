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

use App\Annotations\Rule\Description;
use App\Rst\RstParser;
use App\Value\Lines;
use App\Value\NullViolation;
use App\Value\RuleGroup;
use App\Value\Violation;
use App\Value\ViolationInterface;

/**
 * @Description("Make sure you have a blank line after anchor (`.. anchor:`).")
 */
class BlankLineAfterAnchor extends AbstractRule implements LineContentRule
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

        if ($line->isBlank() || !RstParser::isAnchor($lines->current())) {
            return NullViolation::create();
        }

        $lines->next();

        if (!$lines->valid()) {
            return NullViolation::create();
        }

        while ($lines->valid() && RstParser::isAnchor($lines->current())) {
            $lines->next();
        }

        if ($lines->current()->isBlank()) {
            return NullViolation::create();
        }

        $message = sprintf(
            'Please add a blank line after the anchor "%s"',
            $line->clean()->toString(),
        );

        return Violation::from(
            $message,
            $filename,
            $number + 1,
            $line,
        );
    }
}
