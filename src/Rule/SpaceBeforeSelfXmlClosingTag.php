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

use App\Rst\RstParser;
use App\Value\Lines;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;

/**
 * @no-named-arguments
 */
class SpaceBeforeSelfXmlClosingTag extends AbstractRule implements LineContentRule
{
    public function check(Lines $lines, int $number, string $filename): ViolationInterface
    {
        $lines->seek($number);
        $line = $lines->current()->raw()->toString();

        if (!preg_match('/\/>/', $line)) {
            return NullViolation::create();
        }

        if (!preg_match('/\ \/>/', $line) && !RstParser::isLinkUsage($line)) {
            return Violation::from(
                'Please add space before "/>"',
                $filename,
                $number + 1,
                $line,
            );
        }

        return NullViolation::create();
    }
}
