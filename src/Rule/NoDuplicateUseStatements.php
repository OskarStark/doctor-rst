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
use App\Rst\RstParser;
use App\Value\Lines;
use App\Value\NullViolation;
use App\Value\RuleGroup;
use App\Value\Violation;
use App\Value\ViolationInterface;

#[Description('Ensure there is not same use statement twice')]
final class NoDuplicateUseStatements extends AbstractRule implements LineContentRule
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

        $statements = [];

        while ($lines->valid()
            && !$lines->current()->isDirective()
            && (!preg_match('/^((class|trait) (.*)|\$)/', $lines->current()->clean()->toString()))
        ) {
            if ($lines->current()->clean()->match('/^use (.*);$/')) {
                $statements[] = $lines->current()->clean()->toString();
            }

            $lines->next();
        }

        foreach (array_count_values($statements) as $statement => $count) {
            if (1 < $count) {
                return Violation::from(
                    \sprintf('Please remove duplication of "%s"', $statement),
                    $filename,
                    $number + 1,
                    $line,
                );
            }
        }

        return NullViolation::create();
    }
}
