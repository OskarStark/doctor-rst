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

namespace app\tests\Rule;

use App\Rule\NoBlankLineAfterFilepathInTwigCodeBlock;
use PHPUnit\Framework\TestCase;

class NoBlankLineAfterFilepathInTwigCodeBlockTest extends TestCase
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
            (new NoBlankLineAfterFilepathInTwigCodeBlock())->check(new \ArrayIterator(\is_array($line) ? $line : [$line]), 0)
        );
    }

    public function checkProvider()
    {
        return [
            [
                'Please remove blank line after "{# templates/index.html.twig #}"',
                [
                    '.. code-block:: twig',
                    '',
                    '{# templates/index.html.twig #}',
                    '',
                    '{% set foo = "bar" %}',
                ],
            ],
            [
                null,
                [
                    '.. code-block:: twig',
                    '',
                    '{# templates/index.html.twig #}',
                    '{% set foo = "bar" %}',
                ],
            ],
            [
                null,
                'temp',
            ],
        ];
    }
}
