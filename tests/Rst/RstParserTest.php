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

namespace App\Tests\Rst;

use App\Rst\RstParser;
use PHPUnit\Framework\TestCase;

final class RstParserTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider isLineNumberAnnotationProvider
     */
    public function isLineNumberAnnotation(bool $expected, string $string)
    {
        $this->assertSame($expected, RstParser::isLineNumberAnnotation($string));
    }

    public function isLineNumberAnnotationProvider(): \Generator
    {
        yield [true, 'Line 15'];
        yield [true, 'Line 16 - 18'];
        yield [true, 'Line 16-18'];

        yield [false, ''];
        yield [false, '.. code-block:: php'];
        yield [false, '.. index::'];
    }

    /**
     * @test
     *
     * @dataProvider isDefaultDirectiveProvider
     */
    public function isDefaultDirective(bool $expected, string $string)
    {
        $this->assertSame($expected, RstParser::isDefaultDirective($string));
    }

    public function isDefaultDirectiveProvider(): \Generator
    {
        yield [true, 'this is using the default directive::'];
        yield [true, 'prefixed classes included in doc block comments (``/** ... */``). For example::'];

        yield [false, ''];
        yield [false, '.. code-block:: php'];
        yield [false, '.. index::'];
    }

    /**
     * @test
     *
     * @dataProvider isCommentProvider
     */
    public function isComment(bool $expected, string $string)
    {
        $this->assertSame($expected, RstParser::isComment($string));
    }

    public function isCommentProvider(): \Generator
    {
        yield [true, '.. I am a comment'];

        yield [false, ''];
        yield [false, '.. [1]'];
        yield [false, '.. [1] '];
        yield [false, ' .. [1] '];
    }

    /**
     * @test
     *
     * @dataProvider isFootnoteProvider
     */
    public function isFootnote(bool $expected, string $string)
    {
        $this->assertSame($expected, RstParser::isFootnote($string));
    }

    public function isFootnoteProvider(): \Generator
    {
        yield [false, ''];
        yield [true, '.. [1]'];
        yield [true, '.. [1] '];
        yield [true, ' .. [1] '];
    }

    /**
     * @test
     *
     * @dataProvider isListItemProvider
     */
    public function isListItem(bool $expected, string $string)
    {
        $this->assertSame($expected, RstParser::isListItem($string));
    }

    public function isListItemProvider(): \Generator
    {
        yield [false, ''];
        yield [true, '* Bullet point 1'];
        yield [true, '  * Bullet point 1'];
        yield [true, '#. list item 1'];
        yield [true, '  #. list item 1'];
    }

    /**
     * @test
     *
     * @dataProvider indentionProvider
     */
    public function indention(int $expected, string $string)
    {
        $this->assertSame($expected, RstParser::indention($string));
    }

    public function indentionProvider()
    {
        yield [0, ''];
        yield [1, ' foo'];
        yield [4, '    .. versionchanged:: 3.4'];
    }

    /**
     * @test
     *
     * @dataProvider isLinkDefinitionProvider
     */
    public function isLinkDefinition(bool $expected, string $string)
    {
        $this->assertSame($expected, RstParser::isLinkDefinition($string));
    }

    public function isLinkDefinitionProvider()
    {
        yield [true, '.. _`Symfony`: https://symfony.com'];
        yield [true, '.. _`APCu`: https://github.com/krakjoe/apcu'];
        yield [true, '.. _APCu: https://github.com/krakjoe/apcu'];

        yield [false, '.. _APCu`: https://github.com/krakjoe/apcu'];
        yield [false, '.. _`APCu: https://github.com/krakjoe/apcu'];
        yield [false, ''];
        yield [false, 'I am text::'];
    }

    /**
     * @test
     *
     * @dataProvider isLinkUsageProvider
     */
    public function isLinkUsage(bool $expected, string $string)
    {
        $this->assertSame($expected, RstParser::isLinkUsage($string));
    }

    public function isLinkUsageProvider()
    {
        yield [true, '`Symfony`_'];
        yield [true, '`APCu`_'];

        yield [false, ''];
        yield [false, 'I am text::'];
    }

    /**
     * @test
     *
     * @dataProvider isTableProvider
     */
    public function isTable(bool $expected, string $string)
    {
        $this->assertSame($expected, RstParser::isTable($string));
    }

    public function isTableProvider()
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

    /**
     * @test
     *
     * @dataProvider isHeadlineProvider
     */
    public function isHeadline(bool $expected, string $string)
    {
        $this->assertSame($expected, RstParser::isHeadline($string));
    }

    public function isHeadlineProvider()
    {
        yield [true, '==='];
        yield [true, '~~~'];
        yield [true, '***'];
        yield [true, '---'];
        yield [true, '...'];
        yield [true, '^^^'];

        yield [false, ''];
        yield [false, 'I am text::'];
        yield 'no spaces allowed' => [false, '--- ---'];
    }

    /**
     * @test
     *
     * @dataProvider hasNewlineProvider
     */
    public function hasNewline(bool $expected, string $string)
    {
        $this->assertSame($expected, RstParser::hasNewline($string));
    }

    public function hasNewlineProvider()
    {
        return [
            [
                true,
                'test\n',
            ],
            [
                false,
                '',
            ],
            [
                false,
                'foo',
            ],
        ];
    }

    /**
     * @test
     *
     * @dataProvider cleanProvider
     */
    public function clean(string $expected, string $string)
    {
        $this->assertSame($expected, RstParser::clean($string));
    }

    public function cleanProvider()
    {
        return [
            [
                '.. code-block:: php',
                '.. code-block:: php',
            ],
            [
                '.. code-block:: php',
                '  .. code-block:: php  ',
            ],
            [
                '',
                '\r',
            ],
            [
                '',
                '\n',
            ],
            [
                'when you need to embed a ``\n`` or a Unicode character in a string.',
                'when you need to embed a ``\n`` or a Unicode character in a string.\n',
            ],
            [
                'use Sonata\AdminBundle\Admin\Admin;',
                'use Sonata\AdminBundle\Admin\Admin;',
            ],
            [
                'use Sonata\AdminBundle\Admin\Admin',
                'use Sonata\AdminBundle\Admin\Admin',
            ],
            [
                'use Sonata\AdminBundle\Admin\Admin',
                'use Sonata\AdminBundle\Admin\Admin  ',
            ],
        ];
    }

    /**
     * @test
     *
     * @dataProvider isDirectiveProvider
     */
    public function isDirective(bool $expected, string $string)
    {
        $this->assertSame($expected, RstParser::isDirective($string));
    }

    public function isDirectiveProvider()
    {
        yield [true, 'the following code is php::'];
        yield [true, '.. code-block:: php'];
        yield [true, '.. code-block:: text'];
        yield [true, '.. code-block:: php-annotations'];
        yield [true, ' .. code-block:: php'];
        yield [true, '.. code-block:: html+php'];
        yield [true, '.. image:: /foo/bar.jpg'];
        yield [true, '.. admonition:: Screencast'];

        yield [false, 'foo'];
        yield [false, '.. _`they can be cached`: https://tools.ietf.org/html/draft-ietf-httpbis-p2-semantics-20#section-2.3.4'];
        yield [false, '.. _security-firewalls:'];
    }

    /**
     * @test
     *
     * @dataProvider directiveIsProvider
     */
    public function directiveIs(bool $expected, string $string, string $directive)
    {
        $this->assertSame($expected, RstParser::directiveIs($string, $directive));
    }

    public function directiveIsProvider()
    {
        return [
            [false, '.. note::', RstParser::DIRECTIVE_CODE_BLOCK],
            [true, '.. note::', RstParser::DIRECTIVE_NOTE],
            [true, 'the following code is php::', RstParser::DIRECTIVE_CODE_BLOCK],
            [true, '.. code-block:: php', RstParser::DIRECTIVE_CODE_BLOCK],
            [true, '.. code-block:: text', RstParser::DIRECTIVE_CODE_BLOCK],
            [true, '.. code-block:: rst', RstParser::DIRECTIVE_CODE_BLOCK],
            [true, ' .. code-block:: php', RstParser::DIRECTIVE_CODE_BLOCK],
            [true, '.. code-block:: php-annotations', RstParser::DIRECTIVE_CODE_BLOCK],
            [false, 'foo', RstParser::DIRECTIVE_CODE_BLOCK],
        ];
    }

    /**
     * @test
     *
     * @dataProvider codeBlockDirectiveIsTypeOfProvider
     */
    public function codeBlockDirectiveIsTypeOf(bool $expected, string $string, string $type, bool $strict = false)
    {
        $this->assertSame($expected, RstParser::codeBlockDirectiveIsTypeOf($string, $type, $strict));
    }

    public function codeBlockDirectiveIsTypeOfProvider()
    {
        return [
            [false, '.. note::', RstParser::CODE_BLOCK_PHP],
            [true, 'the following code is php::', RstParser::CODE_BLOCK_PHP],
            [true, '.. code-block:: php', RstParser::CODE_BLOCK_PHP],
            [true, ' .. code-block:: php', RstParser::CODE_BLOCK_PHP],
            [true, ' .. code-block:: php-annotations', RstParser::CODE_BLOCK_PHP_ANNOTATIONS],
            [true, ' .. code-block:: text', RstParser::CODE_BLOCK_TEXT],
            [true, ' .. code-block:: rst', RstParser::CODE_BLOCK_RST],
            [false, 'foo', RstParser::CODE_BLOCK_PHP],
            [true, ' .. code-block:: php', RstParser::CODE_BLOCK_PHP, true],
            [true, ' .. code-block:: php-annotations', RstParser::CODE_BLOCK_PHP_ANNOTATIONS, false],
            [true, ' .. code-block:: html+php', RstParser::CODE_BLOCK_PHP, false],
            [false, ' .. code-block:: html+php', RstParser::CODE_BLOCK_PHP, true],
        ];
    }

    /**
     * @test
     *
     * @dataProvider isBlankLineProvider
     */
    public function isBlankLine(bool $expected, string $string)
    {
        $this->assertSame($expected, RstParser::isBlankLine($string));
    }

    public function isBlankLineProvider()
    {
        return [
            [true, '\r\n'],
            [true, ''],
            [true, ' '],
            [false, 'foo'],
        ];
    }

    /**
     * @test
     *
     * @dataProvider isOptionProvider
     */
    public function isOption(bool $expected, string $string)
    {
        $this->assertSame($expected, RstParser::isOption($string));
    }

    public function isOptionProvider()
    {
        return [
            [true, ':lineos:'],
            [true, ' :lineos: '],
            [true, ':language: text'],
            [true, ' :language: text '],
            [false, ' '],
            [false, ''],
            [false, '.. class::'],
        ];
    }
}
