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
    final public const SHORTHAND = '::';
    final public const DIRECTIVE_CODE_BLOCK = '.. code-block::';
    final public const DIRECTIVE_NOTE = '.. note::';
    final public const DIRECTIVE_WARNING = '.. warning::';
    final public const DIRECTIVE_NOTICE = '.. notice::';
    final public const DIRECTIVE_VERSIONADDED = '.. versionadded::';
    final public const DIRECTIVE_VERSIONCHANGED = '.. versionchanged::';
    final public const DIRECTIVE_DEPRECATED = '.. deprecated::';
    final public const DIRECTIVE_TIP = '.. tip::';
    final public const DIRECTIVE_CAUTION = '.. caution::';
    final public const DIRECTIVE_TOCTREE = '.. toctree::';
    final public const DIRECTIVE_INDEX = '.. index::';
    final public const DIRECTIVE_IMPORTANT = '.. important::';
    final public const DIRECTIVE_CONFIGURATION_BLOCK = '.. configuration-block::';
    final public const DIRECTIVE_BEST_PRACTICE = '.. best-practice::';
    final public const DIRECTIVE_INCLUDE = '.. include::';
    final public const DIRECTIVE_IMAGE = '.. image::';
    final public const DIRECTIVE_ADMONITION = '.. admonition::';
    final public const DIRECTIVE_ROLE = '.. role::';
    final public const DIRECTIVE_FIGURE = '.. figure::';
    final public const DIRECTIVE_SEEALSO = '.. seealso::';
    final public const DIRECTIVE_CLASS = '.. class::';
    final public const DIRECTIVE_RST_CLASS = '.. rst-class::';
    final public const DIRECTIVE_LITERALINCLUDE = '.. literalinclude::';
    final public const DIRECTIVE_CONTENTS = '.. contents::';
    final public const DIRECTIVE_CODEIMPORT = '.. codeimport::';
    final public const DIRECTIVES = [
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
    final public const CODE_BLOCK_PHP = 'php';
    final public const CODE_BLOCK_PHP_ANNOTATIONS = 'php-annotations';
    final public const CODE_BLOCK_PHP_ATTRIBUTES = 'php-attributes';
    final public const CODE_BLOCK_PHP_SYMFONY = 'php-symfony';
    final public const CODE_BLOCK_PHP_STANDALONE = 'php-standalone';
    final public const CODE_BLOCK_XML = 'xml';
    final public const CODE_BLOCK_TWIG = 'twig';
    final public const CODE_BLOCK_JINJA = 'jinja';
    final public const CODE_BLOCK_HTML = 'html';
    final public const CODE_BLOCK_HTML_JINJA = 'html+jinja';
    final public const CODE_BLOCK_HTML_TWIG = 'html+twig';
    final public const CODE_BLOCK_HTML_PHP = 'html+php';
    final public const CODE_BLOCK_YML = 'yml';
    final public const CODE_BLOCK_YAML = 'yaml';
    final public const CODE_BLOCK_BASH = 'bash';
    final public const CODE_BLOCK_SHELL = 'shell';
    final public const CODE_BLOCK_TERMINAL = 'terminal';
    final public const CODE_BLOCK_TEXT = 'text';
    final public const CODE_BLOCK_RST = 'rst';
    final public const CODE_BLOCK_DIFF = 'diff';
    final public const CODE_BLOCK_JSON = 'json';
    final public const CODE_BLOCK_JAVASCRIPT = 'javascript';
    final public const CODE_BLOCK_JS = 'js';
    final public const CODE_BLOCK_JSX = 'jsx';
    final public const CODE_BLOCK_SQL = 'sql';
    final public const CODE_BLOCK_INI = 'ini';
    final public const CODE_BLOCK_VARNISH_3 = 'varnish3';
    final public const CODE_BLOCK_VARNISH_4 = 'varnish4';
    final public const CODE_BLOCK_APACHE = 'apache';
    final public const CODE_BLOCK_CADDY = 'caddy';

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

        if (str_ends_with($line->clean()->toString(), $type)
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

        return \strlen($string) >= 5
            && '.' === $string[0]
            && '.' === $string[1]
            && ' ' === $string[2]
            && '[' === $string[3]
            && [] !== $line->clean()->match(
                '/^\.\. \[[0-9]\]/',
            );
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
