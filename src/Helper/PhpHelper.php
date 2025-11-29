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

namespace App\Helper;

use App\Value\Line;
use App\Value\Lines;

final class PhpHelper
{
    private const string REGEX_NAMESPACE_WITH_TWO_BACKSLASHES = '((?:\\\\{0,2}\w+|\w+\\\\{2})(?:\w+\\\\{2})+)';

    public static function isComment(Line $line): bool
    {
        return [] !== $line->clean()->match('/^(#|\/\/)(.*)/');
    }

    public static function isUsingTwoBackslashes(string $string): bool
    {
        return (bool) preg_match(\sprintf('/%s/', self::REGEX_NAMESPACE_WITH_TWO_BACKSLASHES), $string);
    }

    public static function containsBackslash(string $string): bool
    {
        return (bool) preg_match('/[\\\\]{1}/', $string);
    }

    public static function isFirstLineOfMultilineComment(Line $line): bool
    {
        return $line->clean()->toString() === '/*';
    }

    public static function isLastLineOfMultilineComment(Line $line): bool
    {
        return $line->clean()->toString() === '*/';
    }

    public static function isFirstLineOfDocBlock(Line $line): bool
    {
        return $line->clean()->toString() === '/**';
    }

    public static function isLastLineOfDocBlock(Line $line): bool
    {
        return $line->clean()->toString() === '*/';
    }

    public function isPartOfDocBlock(Lines $lines, int $number): bool
    {
        $lines->seek($number);

        if (self::isFirstLineOfDocBlock($lines->current())
            || self::isLastLineOfDocBlock($lines->current())
        ) {
            return true;
        }

        if (!str_starts_with($lines->current()->clean()->toString(), '*')) {
            return false;
        }

        $i = $number;

        while (1 <= $i) {
            --$i;

            $lines->seek($i);

            if (self::isFirstLineOfDocBlock($lines->current())) {
                return true;
            }

            if (!str_starts_with($lines->current()->clean()->toString(), '*')) {
                return false;
            }
        }

        return false;
    }

    public function isPartOfMultilineComment(Lines $lines, int $number): bool
    {
        $lines->seek($number);

        if (self::isFirstLineOfMultilineComment($lines->current())
            || self::isLastLineOfMultilineComment($lines->current())
        ) {
            return true;
        }

        $i = $number;

        while (1 <= $i) {
            --$i;

            $lines->seek($i);

            if (self::isLastLineOfMultilineComment($lines->current())) {
                return false;
            }

            if (self::isFirstLineOfMultilineComment($lines->current())) {
                return true;
            }
        }

        return false;
    }
}
