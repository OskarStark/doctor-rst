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

namespace App\Helper;

use App\Rst\RstParser;

final class PhpHelper
{
    private const REGEX_NAMESPACE_WITH_ONE_BACKSLASH = '((?:\\\\{0,1}\w+|\w+\\\\{1})(?:\w+\\\\{1})+)';
    private const REGEX_NAMESPACE_WITH_TWO_BACKSLASHES = '((?:\\\\{0,2}\w+|\w+\\\\{2})(?:\w+\\\\{2})+)';

    public static function isComment(string $line): bool
    {
        if (preg_match('/^(#|\/\/)(.*)/', RstParser::clean($line))) {
            return true;
        }

        return false;
    }

    public static function isStartingWithOneBackslash(string $string): bool
    {
        return (bool) preg_match('/^[\\\\]{1}\w+/', $string);
    }

    public static function isStartingWithTwoBackslashes(string $string): bool
    {
        return (bool) preg_match('/^[\\\\]{2}\w+/', $string);
    }

    public static function isUsingOneBackslash(string $string): bool
    {
        return (bool) preg_match(sprintf('/%s/', self::REGEX_NAMESPACE_WITH_ONE_BACKSLASH), $string);
    }

    public static function isUsingTwoBackslashes(string $string): bool
    {
        return (bool) preg_match(sprintf('/%s/', self::REGEX_NAMESPACE_WITH_TWO_BACKSLASHES), $string);
    }

    public static function containsBackslash(string $string): bool
    {
        return (bool) preg_match('/[\\\\]{1}/', $string);
    }
}
