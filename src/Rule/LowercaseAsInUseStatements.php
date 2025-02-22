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

use App\Traits\DirectiveTrait;
use App\Value\Lines;
use App\Value\NullViolation;
use App\Value\RuleGroup;
use App\Value\Violation;
use App\Value\ViolationInterface;

class LowercaseAsInUseStatements extends AbstractRule implements LineContentRule
{
    use DirectiveTrait;

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

        if (!$this->inPhpCodeBlock($lines, $number)) {
            return NullViolation::create();
        }

        if ($matches = $line->clean()->match('/^use (.*) (AS|As|aS) (.*);$/')) {
            /** @var string[] $matches */
            return Violation::from(
                \sprintf('Please use lowercase "as" instead of "%s"', $matches[2]),
                $filename,
                $number + 1,
                $line,
            );
        }

        return NullViolation::create();
    }
}
