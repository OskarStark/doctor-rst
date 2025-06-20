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
use App\Value\RuleGroup;
use App\Value\Violation;
use App\Value\ViolationInterface;

#[Description('Do not use typographic quotes.')]
#[ValidExample('Lorem \'ipsum\' dolor "sit amet"')]
#[InvalidExample('Lorem ‘ipsum’ dolor “sit amet”')]
final class NoTypographicQuotes extends AbstractRule implements LineContentRule
{
    public static function getGroups(): array
    {
        return [RuleGroup::Symfony()];
    }

    public function check(Lines $lines, int $number, string $filename): ViolationInterface
    {
        $lines->seek($number);
        $line = $lines->current();

        $lineContent = $line->raw();

        if ($lineContent->containsAny(['‘', '’'])) {
            return Violation::from(
                'Please use straight single quotes (\' ... \') instead of curly quotes (‘ ... ’)',
                $filename,
                $number + 1,
                $line,
            );
        }

        if ($lineContent->containsAny(['“', '”', '„', '“', '«', '»'])) {
            return Violation::from(
                'Please use straight double quotes (" ... ") instead of typographic quotes (“ ... ”  „ ... “  « ... »)',
                $filename,
                $number + 1,
                $line,
            );
        }

        return NullViolation::create();
    }
}
