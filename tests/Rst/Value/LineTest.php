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

namespace App\Tests\Rst\Value;

use App\Value\Line;
use PHPUnit\Framework\TestCase;

final class LineTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider cleanProvider
     */
    public function clean(string $expected, string $string): void
    {
        static::assertSame($expected, (new Line($string))->clean());
    }

    public function cleanProvider(): array
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
     * @dataProvider isBlankLineProvider
     */
    public function isBlank(bool $expected, string $string): void
    {
        static::assertSame($expected, (new Line($string))->isBlank());
    }

    public function isBlankLineProvider(): array
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
     * @dataProvider indentionProvider
     */
    public function indention(int $expected, string $string): void
    {
        static::assertSame($expected, (new Line($string))->indention());
    }

    public function indentionProvider(): \Generator
    {
        yield [0, ''];
        yield [1, ' foo'];
        yield [4, '    .. versionchanged:: 3.4'];
    }

    /**
     * @test
     *
     * @dataProvider isHeadlineProvider
     */
    public function isHeadline(bool $expected, string $string)
    {
        static::assertSame($expected, (new Line($string))->isHeadline());
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
     * @dataProvider isDirectiveProvider
     */
    public function isDirective(bool $expected, string $string): void
    {
        static::assertSame($expected, (new Line($string))->isDirective());
    }

    public function isDirectiveProvider(): iterable
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
     * @dataProvider isDefaultDirectiveProvider
     */
    public function isDefaultDirective(bool $expected, string $string): void
    {
        static::assertSame($expected, (new Line($string))->isDefaultDirective());
    }

    public function isDefaultDirectiveProvider(): iterable
    {
        yield [true, 'this is using the default directive::'];
        yield [true, 'prefixed classes included in doc block comments (``/** ... */``). For example::'];

        yield [false, ''];
        yield [false, '.. code-block:: php'];
        yield [false, '.. index::'];
    }
}
