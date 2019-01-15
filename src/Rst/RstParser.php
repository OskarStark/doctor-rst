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

namespace App\Rst;

use Webmozart\Assert\Assert;

class RstParser
{
    const DIRECTIVE_CODE_BLOCK = '.. code-block::';
    const DIRECTIVE_NOTE = '.. note::';
    const DIRECTIVE_WARNING = '.. warning::';
    const DIRECTIVE_NOTICE = '.. notice::';
    const DIRECTIVE_VERSIONADDED = '.. versionadded::';
    const DIRECTIVE_TIP = '.. tip::';
    const DIRECTIVE_CAUTION = '.. caution::';
    const DIRECTIVE_TOCTREE = '.. toctree::';
    const DIRECTIVE_INDEX = '.. index::';
    const DIRECTIVE_IMPORTANT = '.. important::';

    const CODE_BLOCK_PHP = 'php';
    const CODE_BLOCK_XML = 'xml';
    const CODE_BLOCK_TWIG = 'twig';
    const CODE_BLOCK_YML = 'yml';
    const CODE_BLOCK_YAML = 'yaml';
    const CODE_BLOCK_BASH = 'bash';
    const CODE_BLOCK_SHELL = 'shell';

    public static function clean(string $string): string
    {
        $string = str_replace(['\n', '\r'], ' ', $string);

        return trim($string);
    }

    public static function isDirective(string $string): bool
    {
        return '..' == substr(ltrim($string), 0, 2) || '::' == substr(self::clean($string), -2, 2);
    }

    public static function directiveIs(string $string, string $directive): bool
    {
        if (!self::isDirective($string)) {
            return false;
        }

        $directivesExcludedCodeBlock = [
            self::DIRECTIVE_NOTE,
            self::DIRECTIVE_WARNING,
            self::DIRECTIVE_NOTICE,
            self::DIRECTIVE_VERSIONADDED,
            self::DIRECTIVE_TIP,
            self::DIRECTIVE_CAUTION,
            self::DIRECTIVE_TOCTREE,
            self::DIRECTIVE_INDEX,
            self::DIRECTIVE_IMPORTANT,
        ];

        Assert::oneOf(
            $directive,
            array_merge(
                [self::DIRECTIVE_CODE_BLOCK],
                $directivesExcludedCodeBlock
            )
        );

        if (strstr($string, $directive)) {
            return true;
        } elseif (self::DIRECTIVE_CODE_BLOCK == $directive && '::' == substr(self::clean($string), -2, 2)) {
            foreach ($directivesExcludedCodeBlock as $other) {
                if (strstr($string, $other)) {
                    return false;
                }
            }

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
                self::CODE_BLOCK_SHELL,
                self::CODE_BLOCK_BASH,
            ]
        );

        $string = self::clean($string);

        if (substr($string, -(\strlen(($type)))) == $type
            || (self::CODE_BLOCK_PHP == $type && '::' == substr(self::clean($string), -2, 2))) {
            return true;
        }

        return false;
    }

    public static function isBlankLine(string $string): bool
    {
        return (bool) empty(RstParser::clean($string));
    }
}
