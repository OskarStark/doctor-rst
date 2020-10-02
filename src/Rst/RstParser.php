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

use App\Value\Line;
use function Symfony\Component\String\u;
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
    const DIRECTIVE_CLASS = '.. class::';
    const DIRECTIVE_RST_CLASS = '.. rst-class::';
    const DIRECTIVE_LITERALINCLUDE = '.. literalinclude::';
    const DIRECTIVE_CONTENTS = '.. contents::';

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
        self::DIRECTIVE_CLASS,
        self::DIRECTIVE_RST_CLASS,
        self::DIRECTIVE_LITERALINCLUDE,
        self::DIRECTIVE_CONTENTS,
    ];

    const CODE_BLOCK_PHP = 'php';
    const CODE_BLOCK_PHP_ANNOTATIONS = 'php-annotations';
    const CODE_BLOCK_PHP_ATTRIBUTES = 'php-attributes';
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
    const CODE_BLOCK_JSX = 'jsx';
    const CODE_BLOCK_SQL = 'sql';
    const CODE_BLOCK_INI = 'ini';
    const CODE_BLOCK_VARNISH_3 = 'varnish3';
    const CODE_BLOCK_VARNISH_4 = 'varnish4';
    const CODE_BLOCK_APACHE = 'apache';

    public static function hasNewline(Line $string): bool
    {
        return '\n' === substr($string->raw(), -2);
    }

    public static function clean(string $string): string
    {
        $string = trim($string);

        if ('\n' === substr($string, -2)) {
            $string = substr($string, 0, -2);
        }

        if ('\r' === substr($string, -2)) {
            $string = substr($string, 0, -2);
        }

        return trim($string);
    }

    /**
     * @todo use regex here
     */
    public static function isDirective(Line $string): bool
    {
        return (0 === strpos(ltrim($string->raw()), '.. ')
            && 0 !== strpos(ltrim($string->raw()), '.. _`')
            && false !== strpos($string->raw(), '::'))
            || self::isDefaultDirective($string);
    }

    public static function directiveIs(Line $line, string $directive, ?bool $strict = false): bool
    {
        if (!self::isDirective($line)) {
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
            self::DIRECTIVE_CLASS,
            self::DIRECTIVE_RST_CLASS,
            self::DIRECTIVE_CONTENTS,
        ];

        Assert::oneOf(
            $directive,
            array_merge(
                [self::DIRECTIVE_CODE_BLOCK],
                $directivesExcludedCodeBlock
            )
        );

        if (false !== strpos($line->raw(), $directive)) {
            return true;
        }

        if (self::DIRECTIVE_CODE_BLOCK === $directive && (!$strict && self::isDefaultDirective($line))) {
            foreach ($directivesExcludedCodeBlock as $other) {
                if (false !== strpos($line->raw(), $other)) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }

    public static function codeBlockDirectiveIsTypeOf(Line $line, string $type, bool $strict = false): bool
    {
        if (!self::directiveIs($line, self::DIRECTIVE_CODE_BLOCK, $strict)) {
            return false;
        }

        Assert::oneOf(
            $type,
            [
                self::CODE_BLOCK_PHP,
                self::CODE_BLOCK_PHP_ANNOTATIONS,
                self::CODE_BLOCK_PHP_ATTRIBUTES,
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
                self::CODE_BLOCK_JSX,
                self::CODE_BLOCK_SQL,
                self::CODE_BLOCK_INI,
                self::CODE_BLOCK_VARNISH_3,
                self::CODE_BLOCK_VARNISH_4,
                self::CODE_BLOCK_APACHE,
            ]
        );

        if (substr($line->clean(), -(\strlen(($type)))) === $type
            || (self::CODE_BLOCK_PHP === $type && self::isDefaultDirective($line))) {
            if (!$strict) {
                return true;
            }

            if (($matches = u($line->clean())->match('/\:\: (.*)$/')) && $type === $matches[1]) {
                return true;
            }

            return false;
        }

        return false;
    }

    public static function isHeadline(Line $line): bool
    {
        if (u($line->raw())->match('/^([\=]+|[\~]+|[\*]+|[\-]+|[\.]+|[\^]+)$/')) {
            return true;
        }

        return false;
    }

    public static function isTable(Line $line): bool
    {
        if (u($line->raw())->match('/^[\=\-]+([\s\=\-]+)?$/')) {
            return true;
        }

        return false;
    }

    public static function isLinkDefinition(Line $line): bool
    {
        if (u($line->raw())->match('/^\.\. _(`([^`]+)`|([^`]+)): (.*)$/')) {
            return true;
        }

        return false;
    }

    public static function isLinkUsage(string $string): bool
    {
        if (u($string)->match('/(?:`[^`]+`|(?:(?!_)\w)+(?:[-._+:](?:(?!_)\w)+)*+)_/')) {
            return true;
        }

        return false;
    }

    public static function isListItem(Line $line): bool
    {
        if (u($line->clean())->match('/^(\* |\#. |[A-Za-z]{1}\) |-\ |[0-9]\. |[0-9]\) )/')) {
            return true;
        }

        return false;
    }

    /**
     * Whether its a footnote ".. [1] " directive or not.
     */
    public static function isFootnote(Line $line): bool
    {
        if (u($line->clean())->match('/^\.\. \[[0-9]\]/')) {
            return true;
        }

        return false;
    }

    /**
     * Whether its and rst comment or not.
     */
    public static function isComment(Line $line): bool
    {
        return !self::isFootnote($line) && preg_match('/^\.\. /', $line->clean());
    }

    public static function isDefaultDirective(Line $line): bool
    {
        return !preg_match('/^\.\. (.*)\:\:/', $line->raw())
            && preg_match('/\:\:$/', $line->raw());
    }

    /**
     * Whether its a line number annotation "Line 15" or "Line 16-18" or not.
     */
    public static function isLineNumberAnnotation(Line $line): bool
    {
        if (u($line->clean())->match('/^Line [0-9]+(\s?-\s?[0-9]+)?$/')) {
            return true;
        }

        return false;
    }

    public static function isOption(Line $line): bool
    {
        if (u($line->clean())->match('/^(:[a-zA-Z]+:).*/')) {
            return true;
        }

        return false;
    }
}
