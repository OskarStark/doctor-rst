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

use App\Attribute\Rule\Description;
use App\Attribute\Rule\InvalidExample;
use App\Attribute\Rule\ValidExample;
use App\Traits\DirectiveTrait;
use App\Value\Lines;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;

#[Description('Ensures that named constructor is used without "new" keyword.')]
#[ValidExample('new Uuid()')]
#[InvalidExample('new Uuid::fromString()')]
final class UseNamedConstructorWithoutNewKeywordRule extends AbstractRule implements LineContentRule
{
    use DirectiveTrait;

    public function check(Lines $lines, int $number, string $filename): ViolationInterface
    {
        $lines->seek($number);
        $line = $lines->current();

        if ($line->isBlank()
            || $line->isDirective()
        ) {
            return NullViolation::create();
        }

        if ([] === $line->raw()->match('/new .*::/')) {
            return NullViolation::create();
        }

        if (!$this->inPhpCodeBlock($lines, $number)) {
            return NullViolation::create();
        }

        return Violation::from(
            'Please do not use "new" keyword with named constructor',
            $filename,
            $number + 1,
            $lines->current()
        );
    }
}
