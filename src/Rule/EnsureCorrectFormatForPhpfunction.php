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
use App\Value\Lines;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;

/**
 * @no-named-arguments
 */
#[Description('Ensure phpfunction directive do not end with ().')]
#[InvalidExample(':phpfunction:`mb_detect_encoding()`.')]
#[ValidExample(':phpfunction:`mb_detect_encoding`.')]
final class EnsureCorrectFormatForPhpfunction extends AbstractRule implements LineContentRule
{
    public function check(Lines $lines, int $number, string $filename): ViolationInterface
    {
        $lines->seek($number);
        $line = $lines->current();

        if ($line->clean()->match('/:phpfunction:`.*\(\)`/')) {
            return Violation::from(
                'Please do not use () at the end of PHP function',
                $filename,
                $number + 1,
                $line,
            );
        }

        return NullViolation::create();
    }
}
