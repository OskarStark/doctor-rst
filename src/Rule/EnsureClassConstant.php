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
use App\Value\Violation;
use App\Value\ViolationInterface;

#[Description('Make sure to use ::class over get_class')]
#[InvalidExample('get_class(new MyClass())')]
#[ValidExample('MyClass::class')]
final class EnsureClassConstant extends AbstractRule implements LineContentRule
{
    public function check(Lines $lines, int $number, string $filename): ViolationInterface
    {
        $lines->seek($number);
        $line = $lines->current();

        if (!RstParser::isPhpDirective($line)) {
            return NullViolation::create();
        }

        $lines->next();
        ++$number;

        if ($lines->current()->isBlank()) {
            $lines->next();
            ++$number;
        }

        $line = $lines->current();

        // PHP
        if ($line->clean()->containsAny('get_class(')) {
            return Violation::from(
                'Please use ::class constant over get_class()',
                $filename,
                $number + 1,
                $line,
            );
        }

        return NullViolation::create();
    }
}
