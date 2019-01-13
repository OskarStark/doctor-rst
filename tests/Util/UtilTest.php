<?php

namespace App\Tests\Util;

use App\Util\Util;
use PHPUnit\Framework\TestCase;

class UtilTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider cleanProvider
     */
    public function clean(string $expected, string $string)
    {
        $this->assertSame($expected, Util::clean($string));
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
        ];
    }

    /**
     * @test
     *
     * @dataProvider isDirectiveProvider
     */
    public function isDirective(bool $expected, string $string)
    {
        $this->assertSame($expected, Util::isDirective($string));
    }

    public function isDirectiveProvider()
    {
        return [
            [true, 'the following code is php::'],
            [true, '.. code-block:: php'],
            [true, ' .. code-block:: php'],
            [false, 'foo'],
        ];
    }

    /**
     * @test
     *
     * @dataProvider directiveIsProvider
     */
    public function directiveIs(bool $expected, string $string, string $directive)
    {
        $this->assertSame($expected, Util::directiveIs($string, $directive));
    }

    public function directiveIsProvider()
    {
        return [
            [false, '.. note::', Util::DIRECTIVE_CODE_BLOCK],
            [true, '.. note::', Util::DIRECTIVE_NOTE],
            [true, 'the following code is php::', Util::DIRECTIVE_CODE_BLOCK],
            [true, '.. code-block:: php', Util::DIRECTIVE_CODE_BLOCK],
            [true, ' .. code-block:: php', Util::DIRECTIVE_CODE_BLOCK],
            [false, 'foo', Util::DIRECTIVE_CODE_BLOCK],
        ];
    }

    /**
     * @test
     *
     * @dataProvider codeBlockDirectiveIsTypeOfProvider
     */
    public function codeBlockDirectiveIsTypeOf(bool $expected, string $string, string $type)
    {
        $this->assertSame($expected, Util::codeBlockDirectiveIsTypeOf($string, $type));
    }

    public function codeBlockDirectiveIsTypeOfProvider()
    {
        return [
            [false, '.. note::', Util::CODE_BLOCK_PHP],
            [true, 'the following code is php::', Util::CODE_BLOCK_PHP],
            [true, '.. code-block:: php', Util::CODE_BLOCK_PHP],
            [true, ' .. code-block:: php', Util::CODE_BLOCK_PHP],
            [false, 'foo', Util::CODE_BLOCK_PHP],
        ];
    }
}
