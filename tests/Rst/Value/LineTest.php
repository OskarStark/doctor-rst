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
        $this->assertSame($expected, (new Line($string))->clean());
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
        $this->assertSame($expected, (new Line($string))->isBlank());
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
        $this->assertSame($expected, (new Line($string))->indention());
    }

    public function indentionProvider(): \Generator
    {
        yield [0, ''];
        yield [1, ' foo'];
        yield [4, '    .. versionchanged:: 3.4'];
    }
}
