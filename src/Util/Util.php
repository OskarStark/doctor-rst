<?php

declare(strict_types=1);

/*
 * This file is part of the rst-checker.
 *
 * (c) Oskar Stark <oskarstark@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Util;

use Webmozart\Assert\Assert;

class Util
{
    const DIRECTIVE_CODE_BLOCK = '.. code-block::';
    const DIRECTIVE_NOTE = '.. note::';
    const DIRECTIVE_WARNING = '.. warning::';
    const DIRECTIVE_NOTICE = '.. notice::';
    const DIRECTIVE_VERSIONADDED = '.. versionadded::';
    const DIRECTIVE_TIP = '.. tip::';
    const DIRECTIVE_CAUTION = '.. caution::';

    const CODE_BLOCK_PHP = 'php';
    const CODE_BLOCK_XML = 'xml';
    const CODE_BLOCK_TWIG = 'twig';
    const CODE_BLOCK_YML = 'yml';
    const CODE_BLOCK_YAML = 'yaml';

    public static function clean(string $string): string
    {
        $string = str_replace(['\n', '\r'], ' ', $string);

        return trim($string);
    }

    public static function isDirective(string $string): bool
    {
        return '..' == substr(ltrim($string), 0, 2);
    }

    public static function directiveIs(string $string, string $directive): bool
    {
        if (!self::isDirective($string)) {
            return false;
        }

        Assert::oneOf(
            $directive,
            [
                self::DIRECTIVE_CODE_BLOCK,
                self::DIRECTIVE_NOTE,
                self::DIRECTIVE_WARNING,
                self::DIRECTIVE_NOTICE,
                self::DIRECTIVE_VERSIONADDED,
                self::DIRECTIVE_TIP,
                self::DIRECTIVE_CAUTION,
            ]
        );

        if (strstr($string, $directive)) {
            return true;
        }

        return false;
    }

    public static function codeBlockDirectiveIsTypeOf(string $string, string $type)
    {
        if (!self::directiveIs($string, self::DIRECTIVE_CODE_BLOCK)) {
            return false;
        }

        Assert::oneOf(
            $type,
            [
                self::CODE_BLOCK_PHP,
                self::CODE_BLOCK_XML,
                self::CODE_BLOCK_TWIG,
                self::CODE_BLOCK_YML,
                self::CODE_BLOCK_YAML,
            ]
        );

        $string = self::clean($string);

        if (substr($string, -(\strlen(($type)))) == $type) {
            return true;
        }

        return false;
    }
}
