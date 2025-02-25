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

namespace App\Tests\Rst\Value;

use App\Tests\UnitTestCase;
use App\Value\Line;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class LineTest extends UnitTestCase
{
    #[Test]
    public function cleanStringEqualsRawAndTrim(): void
    {
        $line = new Line(' test  ');

        self::assertSame($line->clean()->toString(), $line->raw()->trim()->toString());
    }

    #[Test]
    #[DataProvider('cleanProvider')]
    public function clean(string $expected, string $string): void
    {
        self::assertSame($expected, (new Line($string))->clean()->toString());
    }

    /**
     * @return array<array{0: string, 1: string}>
     */
    public static function cleanProvider(): iterable
    {
        yield from [
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

    #[Test]
    #[DataProvider('isBlankProvider')]
    public function isBlank(bool $expected, string $string): void
    {
        self::assertSame($expected, (new Line($string))->isBlank());
    }

    /**
     * @return array<array{0: bool, 1: string}>
     */
    public static function isBlankProvider(): iterable
    {
        yield from [
            [true, '\r\n'],
            [true, ''],
            [true, ' '],
            [false, 'foo'],
        ];
    }

    #[Test]
    #[DataProvider('indentionProvider')]
    public function indention(int $expected, string $string): void
    {
        self::assertSame($expected, (new Line($string))->indention());
    }

    /**
     * @return \Generator<array{0: int, 1: string}>
     */
    public static function indentionProvider(): iterable
    {
        yield [0, ''];
        yield [1, ' foo'];
        yield [4, '    .. versionchanged:: 3.4'];
    }

    #[Test]
    #[DataProvider('isHeadlineProvider')]
    public function isHeadline(bool $expected, string $string): void
    {
        self::assertSame($expected, (new Line($string))->isHeadline());
    }

    /**
     * @return \Generator<array{0: bool, 1: string}>
     */
    public static function isHeadlineProvider(): iterable
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

    #[Test]
    #[DataProvider('isDirectiveProvider')]
    public function isDirective(bool $expected, string $string): void
    {
        self::assertSame($expected, (new Line($string))->isDirective());
    }

    /**
     * @return \Generator<array{0: bool, 1: string}>
     */
    public static function isDirectiveProvider(): iterable
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

    #[Test]
    #[DataProvider('isDefaultDirectiveProvider')]
    public function isDefaultDirective(bool $expected, string $string): void
    {
        self::assertSame($expected, (new Line($string))->isDefaultDirective());
    }

    /**
     * @return \Generator<array{0: bool, 1: string}>
     */
    public static function isDefaultDirectiveProvider(): iterable
    {
        yield [true, 'this is using the default directive::'];
        yield [true, 'prefixed classes included in doc block comments (``/** ... */``). For example::'];
        yield [true, "this is using the default directive::\n"];

        yield [false, ''];
        yield [false, '.. code-block:: php'];
        yield [false, '.. index::'];
    }
}
