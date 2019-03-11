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

namespace App\Tests\Rule;

use App\Rule\BlankLineAfterFilepathInXmlCodeBlock;
use PHPUnit\Framework\TestCase;

class BlankLineAfterFilepathInXmlCodeBlockTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check($expected, $line)
    {
        $this->assertSame(
            $expected,
            (new BlankLineAfterFilepathInXmlCodeBlock())->check(new \ArrayIterator(\is_array($line) ? $line : [$line]), 0)
        );
    }

    public function checkProvider()
    {
        return [
            [
                'Please add a blank line after "<!-- config/services.xml -->"',
                [
                    '.. code-block:: xml',
                    '',
                    '<!-- config/services.xml -->',
                    '<foo\/>',
                ],
            ],
            [
                null,
                [
                    '.. code-block:: xml',
                    '',
                    '<!-- config/services.xml -->',
                    '',
                    '<foo\/>',
                ],
            ],
            [
                'Please add a blank line after "<!--config/services.xml-->"',
                [
                    '.. code-block:: xml',
                    '',
                    '<!--config/services.xml-->',
                    '<foo\/>',
                ],
            ],
            [
                null,
                [
                    '.. code-block:: xml',
                    '',
                    '<!--config/services.xml-->',
                    '',
                    '<foo\/>',
                ],
            ],
            [
                null,
                'temp',
            ],
        ];
    }
}
