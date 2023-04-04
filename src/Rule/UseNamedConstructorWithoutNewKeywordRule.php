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

use App\Traits\DirectiveTrait;
use App\Value\Lines;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;

class UseNamedConstructorWithoutNewKeywordRule extends AbstractRule implements LineContentRule
{
    use DirectiveTrait;

    public function check(Lines $lines, int $number, string $filename): ViolationInterface
    {
        $lines->seek($number);
        $line = $lines->current();

        if ($line->isBlank()) {
            return NullViolation::create();
        }

        if (!$matches = $line->raw()->match('/new .*::/')) {
            return NullViolation::create();
        }

        if (!$this->inPhpCodeBlock($lines, $number)) {
            return NullViolation::create();
        }

        return Violation::from(
            sprintf('Please do not use "new" keyword with named constructor for %s', $matches[0]),
            $filename,
            $number + 1,
            $lines->current()
        );
    }
}
