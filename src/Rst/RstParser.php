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

namespace App\Rst;

use App\Value\Line;
use Webmozart\Assert\Assert;
use function Symfony\Component\String\u;

class RstParser
{
    public const SHORTHAND = '::';
    public const DIRECTIVE_CODE_BLOCK = '.. code-block::';
    public const DIRECTIVE_NOTE = '.. note::';
    public const DIRECTIVE_WARNING = '.. warning::';
    public const DIRECTIVE_NOTICE = '.. notice::';
    public const DIRECTIVE_VERSIONADDED = '.. versionadded::';
    public const DIRECTIVE_VERSIONCHANGED = '.. versionchanged::';
    public const DIRECTIVE_DEPRECATED = '.. deprecated::';
    public const DIRECTIVE_TIP = '.. tip::';
    public const DIRECTIVE_CAUTION = '.. caution::';
    public const DIRECTIVE_TOCTREE = '.. toctree::';
    public const DIRECTIVE_INDEX = '.. index::';
    public const DIRECTIVE_IMPORTANT = '.. important::';
    public const DIRECTIVE_CONFIGURATION_BLOCK = '.. configuration-block::';
    public const DIRECTIVE_BEST_PRACTICE = '.. best-practice::';
    public const DIRECTIVE_INCLUDE = '.. include::';
    public const DIRECTIVE_IMAGE = '.. image::';
    public const DIRECTIVE_ADMONITION = '.. admonition::';
    public const DIRECTIVE_ROLE = '.. role::';
    public const DIRECTIVE_FIGURE = '.. figure::';
    public const DIRECTIVE_SEEALSO = '.. seealso::';
    public const DIRECTIVE_CLASS = '.. class::';
    public const DIRECTIVE_RST_CLASS = '.. rst-class::';
    public const DIRECTIVE_LITERALINCLUDE = '.. literalinclude::';
    public const DIRECTIVE_CONTENTS = '.. contents::';
    public const DIRECTIVE_CODEIMPORT = '.. codeimport::';
    public const DIRECTIVES = [
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
        self::DIRECTIVE_CODEIMPORT,
    ];
    public const CODE_BLOCK_PHP = 'php';
    public const CODE_BLOCK_PHP_ANNOTATIONS = 'php-annotations';
    public const CODE_BLOCK_PHP_ATTRIBUTES = 'php-attributes';
    public const CODE_BLOCK_PHP_SYMFONY = 'php-symfony';
    public const CODE_BLOCK_PHP_STANDALONE = 'php-standalone';
    public const CODE_BLOCK_XML = 'xml';
    public const CODE_BLOCK_TWIG = 'twig';
    public const CODE_BLOCK_JINJA = 'jinja';
    public const CODE_BLOCK_HTML = 'html';
    public const CODE_BLOCK_HTML_JINJA = 'html+jinja';
    public const CODE_BLOCK_HTML_TWIG = 'html+twig';
    public const CODE_BLOCK_HTML_PHP = 'html+php';
    public const CODE_BLOCK_YML = 'yml';
    public const CODE_BLOCK_YAML = 'yaml';
    public const CODE_BLOCK_BASH = 'bash';
    public const CODE_BLOCK_SHELL = 'shell';
    public const CODE_BLOCK_TERMINAL = 'terminal';
    public const CODE_BLOCK_TEXT = 'text';
    public const CODE_BLOCK_RST = 'rst';
    public const CODE_BLOCK_DIFF = 'diff';
    public const CODE_BLOCK_JSON = 'json';
    public const CODE_BLOCK_JAVASCRIPT = 'javascript';
    public const CODE_BLOCK_JS = 'js';
    public const CODE_BLOCK_JSX = 'jsx';
    public const CODE_BLOCK_SQL = 'sql';
    public const CODE_BLOCK_INI = 'ini';
    public const CODE_BLOCK_VARNISH_3 = 'varnish3';
    public const CODE_BLOCK_VARNISH_4 = 'varnish4';
    public const CODE_BLOCK_APACHE = 'apache';
    public const CODE_BLOCK_CADDY = 'caddy';

    public static function isPhpDirective(Line $line): bool
    {
        if ($line->isDefaultDirective()
            || self::codeBlockDirectiveIsTypeOf($line, self::CODE_BLOCK_PHP, true)
            || self::codeBlockDirectiveIsTypeOf($line, self::CODE_BLOCK_PHP_ANNOTATIONS, true)
            || self::codeBlockDirectiveIsTypeOf($line, self::CODE_BLOCK_PHP_ATTRIBUTES, true)
            || self::codeBlockDirectiveIsTypeOf($line, self::CODE_BLOCK_PHP_SYMFONY, true)
            || self::codeBlockDirectiveIsTypeOf($line, self::CODE_BLOCK_PHP_STANDALONE, true)
        ) {
            return true;
        }

        return false;
    }

    public static function directiveIs(Line $line, string $directive, ?bool $strict = false): bool
    {
        if (!$line->isDirective()) {
            return false;
        }

        Assert::oneOf($directive, self::DIRECTIVES);

        if (str_contains($line->raw()->toString(), $directive)) {
            return true;
        }

        if (self::DIRECTIVE_CODE_BLOCK === $directive && (!$strict && $line->isDefaultDirective())) {
            $directivesExcludedCodeBlock = array_diff(self::DIRECTIVES, [$directive]);
            $rawLine = $line->raw()->toString();

            foreach ($directivesExcludedCodeBlock as $other) {
                if (str_contains($rawLine, $other)) {
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
                self::CODE_BLOCK_PHP_SYMFONY,
                self::CODE_BLOCK_PHP_STANDALONE,
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
                self::CODE_BLOCK_CADDY,
            ],
        );

        if (substr($line->clean()->toString(), -\strlen($type)) === $type
            || (self::CODE_BLOCK_PHP === $type && $line->isDefaultDirective())) {
            if (!$strict) {
                return true;
            }

            if (($matches = $line->clean()->match('/\:\: (.*)$/')) && $type === $matches[1]) {
                return true;
            }

            return false;
        }

        return false;
    }

    public static function isTable(Line $line): bool
    {
        return [] !== $line->raw()->match('/^[\=\-]+([\s\=\-]+)?$/');
    }

    public static function isLinkDefinition(Line $line): bool
    {
        return [] !== $line->raw()->match('/^\.\. _(`([^`]+)`|([^`]+)): (.*)$/');
    }

    public static function isLinkUsage(string $string): bool
    {
        return [] !== u($string)->match('/(?:`[^`]+`|(?:(?!_)\w)+(?:[-._+:](?:(?!_)\w)+)*+)_/');
    }

    public static function isListItem(Line $line): bool
    {
        return [] !== $line->clean()->match('/^(\* |\#. |[A-Za-z]{1}\) |-\ |[0-9]\.(\))? |[0-9]\) )/');
    }

    /**
     * Whether its a footnote ".. [1] " directive or not.
     */
    public static function isFootnote(Line $line): bool
    {
        $string = (string) $line->clean();
        return $string[0] === '0' && $string[1] === '.' && $string[2] === ' ' && [] !== $line->clean()->match('/^\.\. \[[0-9]\]/');
    }

    /**
     * Whether its and rst comment or not.
     */
    public static function isComment(Line $line): bool
    {
        return !self::isFootnote($line) && $line->clean()->match('/^\.\. /');
    }

    /**
     * Whether its a line number annotation "Line 15" or "Line 16-18" or not.
     */
    public static function isLineNumberAnnotation(Line $line): bool
    {
        return [] !== $line->clean()->match('/^Line [0-9]+(\s?-\s?[0-9]+)?$/');
    }

    public static function isOption(Line $line): bool
    {
        return [] !== $line->clean()->match('/^(:[a-zA-Z\-]+:).*/');
    }

    public static function isAnchor(Line $line): bool
    {
        return [] !== $line->raw()->match('/^\.\. _.*:$/');
    }
}
