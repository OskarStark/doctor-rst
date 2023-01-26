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

use App\Rst\RstParser;
use App\Value\Lines;
use App\Value\NullViolation;
use App\Value\RuleGroup;
use App\Value\Violation;
use App\Value\ViolationInterface;

class UseDeprecatedDirectiveInsteadOfVersionadded extends AbstractRule implements LineContentRule
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

        if (!RstParser::directiveIs($line, RstParser::DIRECTIVE_VERSIONADDED)) {
            return NullViolation::create();
        }

        $indention = $line->indention();

        $lines->next();

        while ($lines->valid()
            && ($indention < $lines->current()->indention() || $lines->current()->isBlank())
        ) {
            if ($lines->current()->raw()->match('/[^`]deprecated/')) {
                $message = 'Please use ".. deprecated::" instead of ".. versionadded::"';

                return Violation::from(
                    $message,
                    $filename,
                    1,
                    ''
                );
            }

            $lines->next();
        }

        return NullViolation::create();
    }
}
