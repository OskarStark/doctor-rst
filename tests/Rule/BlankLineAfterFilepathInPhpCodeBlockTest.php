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

use App\Rule\BlankLineAfterFilepathInPhpCodeBlock;
use PHPUnit\Framework\TestCase;

class BlankLineAfterFilepathInPhpCodeBlockTest extends TestCase
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
            (new BlankLineAfterFilepathInPhpCodeBlock())->check(new \ArrayIterator(\is_array($line) ? $line : [$line]), 0)
        );
    }

    public function checkProvider()
    {
        return [
            [
                'Please add a blank line after "// src/Handler/Collection.php"',
                [
                    '.. code-block:: php',
                    '',
                    '    // src/Handler/Collection.php',
                    '    namespace App\\Handler;',
                ],
            ],
            [
                null,
                [
                    '.. code-block:: php',
                    '',
                    '    // src/Handler/Collection.php',
                    '',
                    '    namespace App\\Handler;',
                ],
            ],
            [
                null,
                'temp',
            ],
        ];
    }
}
