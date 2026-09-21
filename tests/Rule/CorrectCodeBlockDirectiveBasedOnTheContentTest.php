<?php

declare(strict_types=1);

/**
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
use App\Value\NullViolation;
use App\Value\Violation;

final class CorrectCodeBlockDirectiveBasedOnTheContentTest extends AbstractLineContentRuleTestCase
{
    public function createRule(): CorrectCodeBlockDirectiveBasedOnTheContent
    {
        return new CorrectCodeBlockDirectiveBasedOnTheContent();
    }

    public static function checkProvider(): iterable
    {
        yield 'no directive' => [
            NullViolation::create(),
            new RstSample('I am a text and contain a <tag>'),
        ];

        yield 'another language' => [
            NullViolation::create(),
            new RstSample([
                '.. code-block:: html',
                '',
                '    <div></div>',
            ]),
        ];

        yield 'twig without html' => [
            NullViolation::create(),
            new RstSample([
                '.. code-block:: twig',
                '',
                '    {{ product.name }}',
            ]),
        ];

        yield 'twig with a heart' => [
            NullViolation::create(),
            new RstSample([
                '.. code-block:: twig',
                '',
                '    {{ "I <3 Symfony"|upper }}',
            ]),
        ];

        yield 'twig followed by html outside of the block' => [
            NullViolation::create(),
            new RstSample([
                '.. code-block:: twig',
                '',
                '    {{ product.name }}',
                '',
                'The template renders a <div> element.',
            ]),
        ];

        yield 'html+twig with html' => [
            NullViolation::create(),
            new RstSample([
                '.. code-block:: html+twig',
                '',
                '    <h1>{{ product.name }}</h1>',
            ]),
        ];

        yield 'twig with html' => [
            Violation::from(
                'Please use "html+twig" instead of "twig"',
                'filename',
                1,
                '.. code-block:: twig',
            ),
            new RstSample([
                '.. code-block:: twig',
                '',
                '    <h1>{{ product.name }}</h1>',
            ]),
        ];

        yield 'indented twig with html' => [
            Violation::from(
                'Please use "html+twig" instead of "twig"',
                'filename',
                3,
                '.. code-block:: twig',
            ),
            new RstSample([
                '.. note::',
                '',
                '    .. code-block:: twig',
                '',
                '        <h1>{{ product.name }}</h1>',
            ], 2),
        ];

        yield 'empty html+twig block' => [
            Violation::from(
                'Please use "twig" instead of "html+twig"',
                'filename',
                1,
                '.. code-block:: html+twig',
            ),
            new RstSample('.. code-block:: html+twig'),
        ];

        yield 'html+twig without html' => [
            Violation::from(
                'Please use "twig" instead of "html+twig"',
                'filename',
                1,
                '.. code-block:: html+twig',
            ),
            new RstSample([
                '.. code-block:: html+twig',
                '',
                '    {{ product.name }}',
            ]),
        ];
    }
}
