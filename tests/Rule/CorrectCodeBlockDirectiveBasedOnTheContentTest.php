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

use App\Rule\CorrectCodeBlockDirectiveBasedOnTheContent;
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;

class CorrectCodeBlockDirectiveBasedOnTheContentTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check(?string $expected, RstSample $sample)
    {
        $this->assertSame(
            $expected,
            (new CorrectCodeBlockDirectiveBasedOnTheContent())->check($sample->lines(), $sample->lineNumber())
        );
    }

    /**
     * @return \Generator<array{0: string|null, 1: RstSample}>
     */
    public function checkProvider(): \Generator
    {
        yield [
                null,
                new RstSample(<<<CONTENT
.. code-block:: twig

    {# templates/admin/restock_action.html.twig #}

    {# if the stock is low, include the default action template to render the
       action link. Otherwise, don't include the template so the link is not displayed #}
    {% if item.stock < 10 %}
        {{ include('@EasyAdmin/default/action.html.twig') }}
    {% endif %}
CONTENT
                ),
            ];

        yield [
            'Please use "twig" instead of "html+twig"',
            new RstSample(<<<CONTENT
.. code-block:: html+twig

    {# templates/admin/restock_action.html.twig #}

    {# if the stock is low, include the default action template to render the
       action link. Otherwise, don't include the template so the link is not displayed #}
    {% if item.stock < 10 %}
        {{ include('@EasyAdmin/default/action.html.twig') }}
    {% endif %}
CONTENT
            ),
        ];

        yield [
            null,
            new RstSample('temp'),
        ];
    }
}
