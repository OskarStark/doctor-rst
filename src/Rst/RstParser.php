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
    const DIRECTIVE_VERSIONCHANGED = '.. versionchanged::';
    const DIRECTIVE_DEPRECATED = '.. deprecated::';
    const DIRECTIVE_TIP = '.. tip::';
    const DIRECTIVE_CAUTION = '.. caution::';
    const DIRECTIVE_TOCTREE = '.. toctree::';
    const DIRECTIVE_INDEX = '.. index::';
    const DIRECTIVE_IMPORTANT = '.. important::';
    const DIRECTIVE_CONFIGURATION_BLOCK = '.. configuration-block::';
    const DIRECTIVE_BEST_PRACTICE = '.. best-practice::';
    const DIRECTIVE_INCLUDE = '.. include::';
    const DIRECTIVE_IMAGE = '.. image::';
    const DIRECTIVE_ADMONITION = '.. admonition::';
    const DIRECTIVE_ROLE = '.. role::';
    const DIRECTIVE_FIGURE = '.. figure::';
    const DIRECTIVE_SEEALSO = '.. seealso::';

    const DIRECTIVES = [
        self::DIRECTIVE_CODE_BLOCK,
        self::DIRECTIVE_NOTE,
        self::DIRECTIVE_WARNING,
        self::DIRECTIVE_NOTICE,
        self::DIRECTIVE_VERSIONADDED,
        self::DIRECTIVE_VERSIONCHANGED,
        self::DIRECTIVE_DEPRECATED,
        self::DIRECTIVE_TIP,
        self::DIRECTIVE_CAUTION,
        self::DIRECTIVE_TOCTREE,
        self::DIRECTIVE_INDEX,
        self::DIRECTIVE_IMPORTANT,
        self::DIRECTIVE_CONFIGURATION_BLOCK,
        self::DIRECTIVE_BEST_PRACTICE,
        self::DIRECTIVE_INCLUDE,
        self::DIRECTIVE_IMAGE,
        self::DIRECTIVE_ADMONITION,
        self::DIRECTIVE_ROLE,
        self::DIRECTIVE_FIGURE,
        self::DIRECTIVE_SEEALSO,
    ];

    const CODE_BLOCK_PHP = 'php';
    const CODE_BLOCK_PHP_ANNOTATIONS = 'php-annotations';
    const CODE_BLOCK_XML = 'xml';
    const CODE_BLOCK_TWIG = 'twig';
    const CODE_BLOCK_JINJA = 'jinja';
    const CODE_BLOCK_HTML = 'html';
    const CODE_BLOCK_HTML_JINJA = 'html+jinja';
    const CODE_BLOCK_HTML_TWIG = 'html+twig';
    const CODE_BLOCK_HTML_PHP = 'html+php';
    const CODE_BLOCK_YML = 'yml';
    const CODE_BLOCK_YAML = 'yaml';
    const CODE_BLOCK_BASH = 'bash';
    const CODE_BLOCK_SHELL = 'shell';
    const CODE_BLOCK_TERMINAL = 'terminal';
    const CODE_BLOCK_TEXT = 'text';
    const CODE_BLOCK_RST = 'rst';
    const CODE_BLOCK_DIFF = 'diff';
    const CODE_BLOCK_JSON = 'json';
    const CODE_BLOCK_JAVASCRIPT = 'javascript';
    const CODE_BLOCK_JS = 'js';
    const CODE_BLOCK_SQL = 'sql';
    const CODE_BLOCK_INI = 'ini';
    const CODE_BLOCK_VARNISH_3 = 'varnish3';
    const CODE_BLOCK_VARNISH_4 = 'varnish4';
    const CODE_BLOCK_APACHE = 'apache';

    public static function hasNewline(string $string): bool
    {
        return '\n' === substr($string, -2);
    }

    public static function clean(string $string): string
    {
        $string = trim($string);

        if ('\n' == substr($string, -2)) {
            $string = substr($string, 0, \strlen($string) - 2);
        }

        if ('\r' == substr($string, -2)) {
            $string = substr($string, 0, \strlen($string) - 2);
        }

        return trim($string);
    }

    /**
     * @todo use regex here
     */
    public static function isDirective(string $string): bool
    {
        return ('.. ' == substr(ltrim($string), 0, 3)
            && '.. _`' !== substr(ltrim($string), 0, 5)
            && strstr($string, '::'))
            || self::isDefaultDirective($string);
    }

    public static function directiveIs(string $string, string $directive, ?bool $strict = false): bool
    {
        if (!self::isDirective($string)) {
            return false;
        }

        $directivesExcludedCodeBlock = [
            self::DIRECTIVE_NOTE,
            self::DIRECTIVE_WARNING,
            self::DIRECTIVE_NOTICE,
            self::DIRECTIVE_VERSIONADDED,
            self::DIRECTIVE_DEPRECATED,
            self::DIRECTIVE_TIP,
            self::DIRECTIVE_CAUTION,
            self::DIRECTIVE_TOCTREE,
            self::DIRECTIVE_INDEX,
            self::DIRECTIVE_IMPORTANT,
            self::DIRECTIVE_CONFIGURATION_BLOCK,
            self::DIRECTIVE_BEST_PRACTICE,
            self::DIRECTIVE_INCLUDE,
            self::DIRECTIVE_IMAGE,
            self::DIRECTIVE_ADMONITION,
            self::DIRECTIVE_ROLE,
            self::DIRECTIVE_FIGURE,
            self::DIRECTIVE_SEEALSO,
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
        } elseif (self::DIRECTIVE_CODE_BLOCK == $directive && (!$strict && self::isDefaultDirective($string))) {
            foreach ($directivesExcludedCodeBlock as $other) {
                if (strstr($string, $other)) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }

    public static function codeBlockDirectiveIsTypeOf(string $string, string $type, bool $strict = false): bool
    {
        if (!self::directiveIs($string, self::DIRECTIVE_CODE_BLOCK, $strict)) {
            return false;
        }

        Assert::oneOf(
            $type,
            [
                self::CODE_BLOCK_PHP,
                self::CODE_BLOCK_PHP_ANNOTATIONS,
                self::CODE_BLOCK_XML,
                self::CODE_BLOCK_TWIG,
                self::CODE_BLOCK_JINJA,
                self::CODE_BLOCK_HTML,
                self::CODE_BLOCK_HTML_JINJA,
                self::CODE_BLOCK_HTML_TWIG,
                self::CODE_BLOCK_YML,
                self::CODE_BLOCK_YAML,
                self::CODE_BLOCK_SHELL,
                self::CODE_BLOCK_BASH,
                self::CODE_BLOCK_TERMINAL,
                self::CODE_BLOCK_TEXT,
                self::CODE_BLOCK_RST,
                self::CODE_BLOCK_HTML_PHP,
                self::CODE_BLOCK_DIFF,
                self::CODE_BLOCK_JSON,
                self::CODE_BLOCK_JAVASCRIPT,
                self::CODE_BLOCK_JS,
                self::CODE_BLOCK_SQL,
                self::CODE_BLOCK_INI,
                self::CODE_BLOCK_VARNISH_3,
                self::CODE_BLOCK_VARNISH_4,
                self::CODE_BLOCK_APACHE,
            ]
        );

        $string = self::clean($string);

        if (substr($string, -(\strlen(($type)))) == $type
            || (self::CODE_BLOCK_PHP === $type && self::isDefaultDirective($string))) {
            if (!$strict) {
                return true;
            } else {
                if (preg_match('/\:\: (.*)$/', $string, $matches)) {
                    if ($type === $matches[1]) {
                        return true;
                    }
                }

                return false;
            }
        }

        return false;
    }

    public static function isBlankLine(string $string): bool
    {
        return (bool) empty(RstParser::clean($string));
    }

    public static function isHeadline(string $string): bool
    {
        if (preg_match('/^([\=]+|[\~]+|[\*]+|[\-]+|[\.]+|[\^]+)$/', $string)) {
            return true;
        }

        return false;
    }

    public static function isTable(string $string): bool
    {
        if (preg_match('/^[\=\-]+([\s\=\-]+)?$/', $string)) {
            return true;
        }

        return false;
    }

    public static function indention(string $string): int
    {
        if (preg_match('/^[\s]+/', $string, $matches)) {
            return \strlen($matches[0]);
        }

        return 0;
    }

    public static function isLink(string $string): bool
    {
        if (preg_match('/^\.\. _`(.*)`: (.*)$/', $string)) {
            return true;
        }

        return false;
    }

    public static function isListItem(string $string): bool
    {
        if (preg_match('/^(\* |\#. |[A-Za-z]{1}\) |-\ |[0-9]\. |[0-9]\) )/', self::clean($string))) {
            return true;
        }

        return false;
    }

    /**
     * Whether its a footnote ".. [1] " directive or not.
     */
    public static function isFootnote(string $string): bool
    {
        if (preg_match('/^\.\. \[[0-9]\]/', self::clean($string))) {
            return true;
        }

        return false;
    }

    /**
     * Whether its and rst comment or not.
     */
    public static function isComment(string $string): bool
    {
        if (!self::isFootnote($string) && preg_match('/^\.\. /', self::clean($string))) {
            return true;
        }

        return false;
    }

    public static function isDefaultDirective(string $string): bool
    {
        if (!preg_match('/^\.\. (.*)\:\:/', $string)
            && preg_match('/\:\:$/', $string)) {
            return true;
        }

        return false;
    }
}
