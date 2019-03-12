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
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;

class BlankLineAfterFilepathInTwigCodeBlockTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check($expected, RstSample $sample)
    {
        $this->assertSame(
            $expected,
            (new BlankLineAfterFilepathInTwigCodeBlock())->check($sample->getContent(), $sample->getLineNumber())
        );
    }

    public function checkProvider()
    {
        return [
            [
                'Please add a blank line after "{# templates/index.html.twig #}"',
                new RstSample([
                    '.. code-block:: twig',
                    '',
                    '    {# templates/index.html.twig #}',
                    '    {% set foo = "bar" %}',
                ]),
            ],
            [
                null,
                new RstSample([
                    '.. code-block:: twig',
                    '',
                    '    {# templates/index.html.twig #}',
                    '',
                    '    {% set foo = "bar" %}',
                ]),
            ],
            [
                'Please add a blank line after "{# templates/index.html.twig #}"',
                new RstSample([
                    '.. code-block:: jinja',
                    '',
                    '    {# templates/index.html.twig #}',
                    '    {% set foo = "bar" %}',
                ]),
            ],
            [
                null,
                new RstSample([
                    '.. code-block:: jinja',
                    '',
                    '    {# templates/index.html.twig #}',
                    '',
                    '    {% set foo = "bar" %}',
                ]),
            ],
            [
                'Please add a blank line after "{# templates/index.html.twig #}"',
                new RstSample([
                    '.. code-block:: html+jinja',
                    '',
                    '    {# templates/index.html.twig #}',
                    '    {% set foo = "bar" %}',
                ]),
            ],
            [
                null,
                new RstSample([
                    '.. code-block:: html+jinja',
                    '',
                    '    {# templates/index.html.twig #}',
                    '',
                    '    {% set foo = "bar" %}',
                ]),
            ],
            [
                'Please add a blank line after "{# templates/index.html.twig #}"',
                new RstSample([
                    '.. code-block:: html+twig',
                    '',
                    '    {# templates/index.html.twig #}',
                    '    {% set foo = "bar" %}',
                ]),
            ],
            [
                null,
                new RstSample([
                    '.. code-block:: html+twig',
                    '',
                    '    {# templates/index.html.twig #}',
                    '    {# a comment #}',
                    '    {% set foo = "bar" %}',
                ]),
            ],
            [
                null,
                new RstSample([
                    '.. code-block:: html+twig',
                    '',
                    '    {# templates/index.html.twig #}',
                    '',
                    '    {% set foo = "bar" %}',
                ]),
            ],
            [
                null,
                new RstSample('temp'),
            ],
        ];
    }
}
