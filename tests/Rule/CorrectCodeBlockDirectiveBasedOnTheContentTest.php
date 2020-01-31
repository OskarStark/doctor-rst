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
     * @dataProvider containsHtmlProvider
     */
    public function containsHtml(bool $expected, RstSample $sample)
    {
        $this->assertSame(
            $expected,
            (new CorrectCodeBlockDirectiveBasedOnTheContent())->containsHtml($sample->lines()->toIterator(), 0)
        );
    }

    /**
     * @return \Generator<array{0: bool, 1: RstSample}>
     */
    public function containsHtmlProvider(): \Generator
    {
        yield [
            false,
            new RstSample(
                <<<CONTENT
{# templates/admin/tag_collection.html.twig #}
{% set colors = field_options.label_colors|default(['primary']) %}

{% for tag in value %}
    I love you <3
{% endfor %}
CONTENT
            ),
        ];

        yield [
            true,
            new RstSample(
                <<<CONTENT
{# templates/admin/tag_collection.html.twig #}
{% set colors = field_options.label_colors|default(['primary']) %}

{% for tag in value %}
    <span class="label label-{{ cycle(colors, loop.index) }}">{{ tag }}</span>
{% endfor %}
CONTENT
            ),
        ];

        yield [
            false,
            new RstSample(
                <<<CONTENT
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
            false,
            new RstSample(
                <<<CONTENT
    {# templates/bundles/EasyAdminBundle/default/field_string.html.twig #}
    {% if field_options.trans|default(false) %}
        {# translate fields defined as "translatable" #}
        {{ value|trans({}, field_options.domain|default('messages')) }}
    {% else %}
        {# if not translatable, simply include the default template #}
        {{ include('@!EasyAdmin/default/field_string.html.twig') }}
    {% endif %}
CONTENT
            ),
        ];
    }

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
            null,
            new RstSample(<<<CONTENT
.. code-block:: twig

    {# templates/bundles/EasyAdminBundle/default/field_string.html.twig #}
    {% if field_options.trans|default(false) %}
        {# translate fields defined as "translatable" #}
        {{ value|trans({}, field_options.domain|default('messages')) }}
    {% else %}
        {# if not translatable, simply include the default template #}
        {{ include('@!EasyAdmin/default/field_string.html.twig') }}
    {% endif %}
CONTENT
            ),
        ];

        yield [
            null,
            new RstSample(<<<CONTENT
.. code-block:: twig

    {# templates/admin/tag_collection.html.twig #}
    {% set colors = field_options.label_colors|default(['primary']) %}

    {% for tag in value %}
        I love you <3
    {% endfor %}
CONTENT
            ),
        ];

        yield [
            null,
            new RstSample(<<<CONTENT
.. code-block:: html+twig

    {# templates/admin/tag_collection.html.twig #}
    {% set colors = field_options.label_colors|default(['primary']) %}

    {% for tag in value %}
        <span class="label label-{{ cycle(colors, loop.index) }}">{{ tag }}</span>
    {% endfor %}
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
