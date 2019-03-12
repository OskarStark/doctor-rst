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

use App\Rule\BlankLineAfterFilepathInTwigCodeBlock;
use PHPUnit\Framework\TestCase;

class BlankLineAfterFilepathInTwigCodeBlockTest extends TestCase
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
            (new BlankLineAfterFilepathInTwigCodeBlock())->check(new \ArrayIterator(\is_array($line) ? $line : [$line]), 0)
        );
    }

    public function checkProvider()
    {
        return [
            [
                'Please add a blank line after "{# templates/index.html.twig #}"',
                [
                    '.. code-block:: twig',
                    '',
                    '    {# templates/index.html.twig #}',
                    '    {% set foo = "bar" %}',
                ],
            ],
            [
                null,
                [
                    '.. code-block:: twig',
                    '',
                    '    {# templates/index.html.twig #}',
                    '',
                    '    {% set foo = "bar" %}',
                ],
            ],
            [
                'Please add a blank line after "{# templates/index.html.twig #}"',
                [
                    '.. code-block:: jinja',
                    '',
                    '    {# templates/index.html.twig #}',
                    '    {% set foo = "bar" %}',
                ],
            ],
            [
                null,
                [
                    '.. code-block:: jinja',
                    '',
                    '    {# templates/index.html.twig #}',
                    '',
                    '    {% set foo = "bar" %}',
                ],
            ],
            [
                'Please add a blank line after "{# templates/index.html.twig #}"',
                [
                    '.. code-block:: html+jinja',
                    '',
                    '    {# templates/index.html.twig #}',
                    '    {% set foo = "bar" %}',
                ],
            ],
            [
                null,
                [
                    '.. code-block:: html+jinja',
                    '',
                    '    {# templates/index.html.twig #}',
                    '',
                    '    {% set foo = "bar" %}',
                ],
            ],
            [
                'Please add a blank line after "{# templates/index.html.twig #}"',
                [
                    '.. code-block:: html+twig',
                    '',
                    '    {# templates/index.html.twig #}',
                    '    {% set foo = "bar" %}',
                ],
            ],
            [
                null,
                [
                    '.. code-block:: html+twig',
                    '',
                    '    {# templates/index.html.twig #}',
                    '',
                    '    {% set foo = "bar" %}',
                ],
            ],
            [
                null,
                'temp',
            ],
        ];
    }
}
