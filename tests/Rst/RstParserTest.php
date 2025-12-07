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

namespace App\Tests\Rst;

use App\Rst\RstParser;
use App\Tests\UnitTestCase;
use App\Value\Line;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class RstParserTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('isPhpDirectiveProvider')]
    public function isPhpDirective(bool $expected, string $string): void
    {
        self::assertSame($expected, RstParser::isPhpDirective(new Line($string)));
    }

    /**
     * @return \Generator<array{0: bool, 1: string}>
     */
    public static function isPhpDirectiveProvider(): iterable
    {
        foreach (self::phpCodeBlocks() as $phpCodeBlock) {
            yield [true, $phpCodeBlock];
        }

        yield [false, ''];
        yield [false, '.. code-block:: html+php'];
        yield [false, '.. index::'];
    }

    #[Test]
    #[DataProvider('isLineNumberAnnotationProvider')]
    public function isLineNumberAnnotation(bool $expected, string $string): void
    {
        self::assertSame($expected, RstParser::isLineNumberAnnotation(new Line($string)));
    }

    /**
     * @return \Generator<array{0: bool, 1: string}>
     */
    public static function isLineNumberAnnotationProvider(): iterable
    {
        yield [true, 'Line 15'];
        yield [true, 'Line 16 - 18'];
        yield [true, 'Line 16-18'];

        yield [false, ''];
        yield [false, '.. code-block:: php'];
        yield [false, '.. index::'];
    }

    #[Test]
    #[DataProvider('isCommentProvider')]
    public function isComment(bool $expected, string $string): void
    {
        self::assertSame($expected, RstParser::isComment(new Line($string)));
    }

    /**
     * @return \Generator<array{0: bool, 1: string}>
     */
    public static function isCommentProvider(): iterable
    {
        yield [true, '.. I am a comment'];

        yield [false, ''];
        yield [false, '.. [1]'];
        yield [false, '.. [1] '];
        yield [false, ' .. [1] '];
    }

    #[Test]
    #[DataProvider('isFootnoteProvider')]
    public function isFootnote(bool $expected, string $string): void
    {
        self::assertSame($expected, RstParser::isFootnote(new Line($string)));
    }

    /**
     * @return \Generator<array{0: bool, 1: string}>
     */
    public static function isFootnoteProvider(): iterable
    {
        yield [false, ''];
        yield [true, '.. [1]'];
        yield [true, '.. [1] '];
        yield [true, ' .. [1] '];
    }

    #[Test]
    #[DataProvider('isListItemProvider')]
    public function isListItem(bool $expected, string $string): void
    {
        self::assertSame($expected, RstParser::isListItem(new Line($string)));
    }

    /**
     * @return \Generator<array{0: bool, 1: string}>
     */
    public static function isListItemProvider(): iterable
    {
        yield [false, ''];
        yield [true, '* Bullet point 1'];
        yield [true, '  * Bullet point 1'];
        yield [true, '#. list item 1'];
        yield [true, '  #. list item 1'];
    }

    #[Test]
    #[DataProvider('isLinkDefinitionProvider')]
    public function isLinkDefinition(bool $expected, string $string): void
    {
        self::assertSame($expected, RstParser::isLinkDefinition(new Line($string)));
    }

    /**
     * @return \Generator<array{0: bool, 1: string}>
     */
    public static function isLinkDefinitionProvider(): iterable
    {
        yield [true, '.. _`Symfony`: https://symfony.com'];
        yield [true, '.. _`APCu`: https://github.com/krakjoe/apcu'];
        yield [true, '.. _APCu: https://github.com/krakjoe/apcu'];
        yield [true, '   .. _`Pimple`: https://github.com/silexphp/Pimple'];

        yield [false, '.. _APCu`: https://github.com/krakjoe/apcu'];
        yield [false, '.. _`APCu: https://github.com/krakjoe/apcu'];
        yield [false, ''];
        yield [false, 'I am text::'];
    }

    #[Test]
    #[DataProvider('isLinkUsageProvider')]
    public function isLinkUsage(bool $expected, string $string): void
    {
        self::assertSame($expected, RstParser::isLinkUsage($string));
    }

    /**
     * @return \Generator<array{0: bool, 1: string}>
     */
    public static function isLinkUsageProvider(): iterable
    {
        yield [true, '`Symfony`_'];
        yield [true, '`APCu`_'];

        yield [false, ''];
        yield [false, 'I am text::'];
    }

    #[Test]
    #[DataProvider('isTableProvider')]
    public function isTable(bool $expected, string $string): void
    {
        self::assertSame($expected, RstParser::isTable(new Line($string)));
    }

    /**
     * @return \Generator<array{0: bool, 1: string}>
     */
    public static function isTableProvider(): iterable
    {
        yield [true, '==='];
        yield [true, '=== ==='];
        yield [true, '=== === ==='];
        yield [true, '------------------ -------- -------- ------ ----------------------------------------------'];

        yield [false, '~~~'];
        yield [false, '***'];
        yield [false, '...'];
        yield [false, '^^^'];
        yield [false, ''];
        yield [false, 'I am text::'];
    }

    #[Test]
    #[DataProvider('directiveIsProvider')]
    public function directiveIs(bool $expected, string $string, string $directive): void
    {
        self::assertSame($expected, RstParser::directiveIs(new Line($string), $directive));
    }

    /**
     * @return \Generator<array{0: bool, 1: string, 2: string}>
     */
    public static function directiveIsProvider(): iterable
    {
        yield [false, '.. note::', RstParser::DIRECTIVE_CODE_BLOCK];
        yield [true, '.. note::', RstParser::DIRECTIVE_NOTE];
        yield [true, 'the following code is php::', RstParser::DIRECTIVE_CODE_BLOCK];
        yield [true, '.. code-block:: php', RstParser::DIRECTIVE_CODE_BLOCK];
        yield [true, '.. code-block:: text', RstParser::DIRECTIVE_CODE_BLOCK];
        yield [true, '.. code-block:: rst', RstParser::DIRECTIVE_CODE_BLOCK];
        yield [true, ' .. code-block:: php', RstParser::DIRECTIVE_CODE_BLOCK];
        yield [true, '.. code-block:: php-annotations', RstParser::DIRECTIVE_CODE_BLOCK];
        yield [true, '.. code-block:: caddy', RstParser::DIRECTIVE_CODE_BLOCK];
        yield [false, 'foo', RstParser::DIRECTIVE_CODE_BLOCK];
    }

    #[Test]
    #[DataProvider('codeBlockDirectiveIsTypeOfProvider')]
    public function codeBlockDirectiveIsTypeOf(bool $expected, string $string, string $type, bool $strict = false): void
    {
        self::assertSame($expected, RstParser::codeBlockDirectiveIsTypeOf(new Line($string), $type, $strict));
    }

    /**
     * @return \Generator<array{0: bool, 1: string, 2: string}>
     */
    public static function codeBlockDirectiveIsTypeOfProvider(): iterable
    {
        yield [false, '.. note::', RstParser::CODE_BLOCK_PHP];
        yield [true, 'the following code is php::', RstParser::CODE_BLOCK_PHP];
        yield [true, '.. code-block:: php', RstParser::CODE_BLOCK_PHP];
        yield [true, ' .. code-block:: php', RstParser::CODE_BLOCK_PHP];
        yield [true, ' .. code-block:: php-annotations', RstParser::CODE_BLOCK_PHP_ANNOTATIONS];
        yield [true, ' .. code-block:: php-attributes', RstParser::CODE_BLOCK_PHP_ATTRIBUTES];
        yield [true, ' .. code-block:: php-symfony', RstParser::CODE_BLOCK_PHP_SYMFONY];
        yield [true, ' .. code-block:: php-standalone', RstParser::CODE_BLOCK_PHP_STANDALONE];
        yield [true, ' .. code-block:: text', RstParser::CODE_BLOCK_TEXT];
        yield [true, ' .. code-block:: rst', RstParser::CODE_BLOCK_RST];
        yield [true, ' .. code-block:: caddy', RstParser::CODE_BLOCK_CADDY];
        yield [false, 'foo', RstParser::CODE_BLOCK_PHP];
        yield [true, ' .. code-block:: php', RstParser::CODE_BLOCK_PHP, true];
        yield [true, ' .. code-block:: php-annotations', RstParser::CODE_BLOCK_PHP_ANNOTATIONS, false];
        yield [true, ' .. code-block:: php-attributes', RstParser::CODE_BLOCK_PHP_ATTRIBUTES, false];
        yield [true, ' .. code-block:: php-symfony', RstParser::CODE_BLOCK_PHP_SYMFONY, false];
        yield [true, ' .. code-block:: php-standalone', RstParser::CODE_BLOCK_PHP_STANDALONE, false];
        yield [true, ' .. code-block:: html+php', RstParser::CODE_BLOCK_PHP, false];
        yield [false, ' .. code-block:: html+php', RstParser::CODE_BLOCK_PHP, true];
    }

    #[Test]
    #[DataProvider('isOptionProvider')]
    public function isOption(bool $expected, string $string): void
    {
        self::assertSame($expected, RstParser::isOption(new Line($string)));
    }

    /**
     * @return \Generator<array{0: bool, 1: string}>
     */
    public static function isOptionProvider(): iterable
    {
        yield [true, ':lineos:'];
        yield [true, ' :lineos: '];
        yield [true, ':language: text'];
        yield [true, ' :language: text '];
        yield [true, ' :emphasize-lines: 3,11'];

        yield [false, ' '];
        yield [false, ''];
        yield [false, '.. class::'];
        yield [false, '.. _env-var-processors:'];
    }

    #[Test]
    #[DataProvider('isAnchorProvider')]
    public function isAnchor(bool $expected, string $string): void
    {
        self::assertSame($expected, RstParser::isAnchor(new Line($string)));
    }

    /**
     * @return \Generator<array{0: bool, 1: string}>
     */
    public static function isAnchorProvider(): iterable
    {
        yield [true, '.. _env-var-processors:'];

        yield [false, ' '];
        yield [false, ''];
        yield [false, '.. _`foo-bar`: https://google.com'];
        yield [false, '.. _foo-bar: https://google.com'];
        yield [false, '.. _foo: https://google.com'];
    }
}
