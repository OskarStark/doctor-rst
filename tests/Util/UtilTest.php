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
            [true, '.. code-block:: php'],
            [true, ' .. code-block:: php'],
            [false, 'foo'],
        ];
    }
}
