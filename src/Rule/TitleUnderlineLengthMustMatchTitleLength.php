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

use App\Value\Lines;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;

class TitleUnderlineLengthMustMatchTitleLength extends AbstractRule implements LineContentRule
{
    public function check(Lines $lines, int $number, string $filename): ViolationInterface
    {
        $lines->seek($number);
        $line = $lines->current();

        if (!$line->isHeadline()) {
            return NullViolation::create();
        }

        $headLineLength = mb_strlen($line->clean()->toString());
        $lines->seek($number - 1);

        if ($lines->valid()
            && !$lines->current()->isBlank()
            && !(mb_strlen($lines->current()->clean()->toString()) === $headLineLength)
        ) {
            return Violation::from(
                sprintf(
                    'Please ensure title "%s" and underline length are matching',
                    $lines->current()->raw()->replace('`', '')->toString(),
                ),
                $filename,
                $number,
                $lines->current(),
            );
        }

        return NullViolation::create();
    }
}
