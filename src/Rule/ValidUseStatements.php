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

use App\Value\Lines;
use App\Value\NullViolation;
use App\Value\RuleGroup;
use App\Value\Violation;
use App\Value\ViolationInterface;

class ValidUseStatements extends AbstractRule implements LineContentRule
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

        /*
         * @todo do it in one regex instead of regex + string search
         */
        if ($line->clean()->match('/^use (.*);$/') && false !== strpos($line->clean()->toString(), '\\\\')) {
            return Violation::from(
                'Please do not escape the backslashes in a use statement.',
                $filename,
                $number + 1,
                $line
            );
        }

        return NullViolation::create();
    }
}
