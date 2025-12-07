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

use App\Rule\UseDoubleBackticksForInlineLiterals;
use App\Tests\RstSample;
use App\Value\NullViolation;
use App\Value\Violation;

final class UseDoubleBackticksForInlineLiteralsTest extends AbstractLineContentRuleTestCase
{
    public function createRule(): UseDoubleBackticksForInlineLiterals
    {
        return new UseDoubleBackticksForInlineLiterals();
    }

    public static function checkProvider(): iterable
    {
        yield 'valid - double backticks' => [
            NullViolation::create(),
            new RstSample('Please use ``vector`` for this.'),
        ];

        yield 'valid - role with single backticks' => [
            NullViolation::create(),
            new RstSample('See :ref:`my-reference` for details.'),
        ];

        yield 'valid - doc role' => [
            NullViolation::create(),
            new RstSample('Read :doc:`/components/store` for more info.'),
        ];

        yield 'valid - class role' => [
            NullViolation::create(),
            new RstSample('The :class:`Symfony\\Component\\HttpFoundation\\Request` class.'),
        ];

        yield 'valid - method role' => [
            NullViolation::create(),
            new RstSample('Call :method:`Symfony\\Component\\HttpFoundation\\Request::getContent`'),
        ];

        yield 'valid - no backticks' => [
            NullViolation::create(),
            new RstSample('This is a plain text line.'),
        ];

        yield 'valid - empty line' => [
            NullViolation::create(),
            new RstSample(''),
        ];

        yield 'invalid - single backticks for literal' => [
            Violation::from(
                'Please use double backticks for inline literals: `vector` should be ``vector``',
                'filename',
                1,
                'Please use `vector` for this.',
            ),
            new RstSample('Please use `vector` for this.'),
        ];

        yield 'invalid - single backticks for class name' => [
            Violation::from(
                'Please use double backticks for inline literals: `ToolboxInterface` should be ``ToolboxInterface``',
                'filename',
                1,
                'The `ToolboxInterface` provides tools.',
            ),
            new RstSample('The `ToolboxInterface` provides tools.'),
        ];

        yield 'invalid - single backticks for method' => [
            Violation::from(
                'Please use double backticks for inline literals: `getTools()` should be ``getTools()``',
                'filename',
                1,
                'Use `getTools()` to retrieve tools.',
            ),
            new RstSample('Use `getTools()` to retrieve tools.'),
        ];

        yield 'invalid - single backticks for table name' => [
            Violation::from(
                'Please use double backticks for inline literals: `blog` should be ``blog``',
                'filename',
                1,
                'Create a table named `blog`.',
            ),
            new RstSample('Create a table named `blog`.'),
        ];

        yield 'valid - RST link with trailing underscore' => [
            NullViolation::create(),
            new RstSample('it by using the native PHP `reflection`_. This method is defined in the'),
        ];

        yield 'valid - RST link with trailing underscore at end of line' => [
            NullViolation::create(),
            new RstSample('See the `official documentation`_'),
        ];

        yield 'valid - RST anonymous link with double underscore' => [
            NullViolation::create(),
            new RstSample('Check the `example`__ for more details.'),
        ];

        yield 'valid - single backticks inside code block' => [
            NullViolation::create(),
            new RstSample([
                '.. code-block:: yaml',
                '',
                '    # you can add more services here (e.g. the `serializer`)',
            ], 2),
        ];

        yield 'valid - single backticks inside php code block' => [
            NullViolation::create(),
            new RstSample([
                '.. code-block:: php',
                '',
                '    // Using the `foo` variable',
            ], 2),
        ];

        yield 'valid - multiple ref roles with custom titles on same line' => [
            NullViolation::create(),
            new RstSample(':ref:`Rendering forms <rendering-forms>` and :ref:`processing forms <processing-forms>`'),
        ];

        yield 'valid - multiple RST links on same line' => [
            NullViolation::create(),
            new RstSample('Validates that a value is a valid `Universally unique identifier (UUID)`_ per `RFC 4122`_.'),
        ];

        yield 'valid - ref role followed by RST link' => [
            NullViolation::create(),
            new RstSample('* :ref:`Webpack Encore <frontend-webpack-encore>` is built with `Node.js`_'),
        ];

        yield 'valid - multiple RST links with or separator' => [
            NullViolation::create(),
            new RstSample('and `JSON Web Tokens (JWT)`_ or `SAML2 (XML structures)`_. Please refer to the'),
        ];

        yield 'valid - doc role followed by RST link' => [
            NullViolation::create(),
            new RstSample('and Because :doc:`the Form component </forms>` as well as `API Platform`_ internally'),
        ];

        yield 'valid - RST link target definition with backticks' => [
            NullViolation::create(),
            new RstSample('.. _`same-origin`: https://en.wikipedia.org/wiki/Same-origin_policy'),
        ];

        yield 'valid - indented RST link target definition with backticks' => [
            NullViolation::create(),
            new RstSample('   .. _`Pimple`: https://github.com/silexphp/Pimple'),
        ];

        yield 'valid - RST internal anchor without URL' => [
            NullViolation::create(),
            new RstSample('.. _`upgrade-minor-symfony-composer`:'),
        ];

        yield 'valid - RST internal anchor without backticks or URL' => [
            NullViolation::create(),
            new RstSample('.. _upgrade-minor-symfony-code:'),
        ];

        yield 'valid - doc role followed by comma and another doc role' => [
            NullViolation::create(),
            new RstSample(':doc:`routing </routing>`, or rendering :doc:`controllers </controller>`;'),
        ];

        yield 'valid - RST link followed by comma and doc role' => [
            NullViolation::create(),
            new RstSample('`Templating`_, :doc:`Security </security>`, :doc:`Form </components/form>`,'),
        ];

        yield 'valid - multiple doc roles with commas' => [
            NullViolation::create(),
            new RstSample('(:doc:`Apcu </components/cache/adapters/apcu_adapter>`, :doc:`Memcached </components/cache/adapters/memcached_adapter>`,'),
        ];

        yield 'valid - doc role followed by semicolon and another doc role' => [
            NullViolation::create(),
            new RstSample(':doc:`one </one>`; :doc:`two </two>`'),
        ];

        yield 'valid - RST link followed by text and class role' => [
            NullViolation::create(),
            new RstSample('Symfony comes with two minimalist `PSR-3`_ loggers: :class:`Symfony\\Component\\HttpKernel\\Log\\Logger`'),
        ];

        yield 'valid - ref role followed by colon and method role' => [
            NullViolation::create(),
            new RstSample('* :ref:`List of properties <property-info-list>`: :method:`Symfony\\Component\\PropertyInfo\\PropertyListExtractorInterface::getProperties`'),
        ];

        yield 'valid - ref role followed by colon space and method role' => [
            NullViolation::create(),
            new RstSample('* :ref:`Property type <property-info-type>`: :method:`Symfony\\Component\\PropertyInfo\\PropertyTypeExtractorInterface::getTypes`'),
        ];

        yield 'valid - ref followed by colon and multiple method roles' => [
            NullViolation::create(),
            new RstSample('* :ref:`Property description <property-info-description>`: :method:`Symfony\\Component\\PropertyInfo\\PropertyDescriptionExtractorInterface::getShortDescription` and :method:`Symfony\\Component\\PropertyInfo\\PropertyDescriptionExtractorInterface::getLongDescription`'),
        ];

        yield 'valid - ref followed by double space colon and method role' => [
            NullViolation::create(),
            new RstSample('* :ref:`Property initializable through the constructor <property-info-initializable>`:  :method:`Symfony\\Component\\PropertyInfo\\PropertyInitializableExtractorInterface::isInitializable`'),
        ];

        yield 'valid - closing angle bracket followed by period See and doc role' => [
            NullViolation::create(),
            new RstSample('<services-wire-specific-service>`. See :doc:`/service_container/debug`.'),
        ];

        yield 'valid - closing angle bracket followed by text and ref role' => [
            NullViolation::create(),
            new RstSample('<service-container-creating-service>`. If you\'re using the :ref:`default'),
        ];
    }
}
