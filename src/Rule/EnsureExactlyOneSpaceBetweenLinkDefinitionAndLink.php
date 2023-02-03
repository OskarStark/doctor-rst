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
use App\Rst\RstParser;
use App\Value\Lines;
use App\Value\NullViolation;
use App\Value\RuleGroup;
use App\Value\Violation;
use App\Value\ViolationInterface;

/**
 * @Description("Ensure exactly one space between link definition and link.")
 *
 * @InvalidExample(".. _DOCtor-RST:     https://github.com/OskarStark/DOCtor-RST")
 *
 * @ValidExample(".. _DOCtor-RST: https://github.com/OskarStark/DOCtor-RST")
 */
class EnsureExactlyOneSpaceBetweenLinkDefinitionAndLink extends AbstractRule implements LineContentRule
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

        if (!RstParser::isLinkDefinition($line)) {
            return NullViolation::create();
        }

        if ($line->clean()->containsAny(':  ')) {
            return Violation::from(
                'Please use only one whitespace between the link definition and the link.',
                $filename,
                $number + 1,
                $line
            );
        }

        return NullViolation::create();
    }
}
