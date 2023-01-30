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

use App\Rule\ValidInlineHighlightedNamespaces;
use App\Tests\RstSample;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;

final class ValidInlineHighlightedNamespacesTest extends \App\Tests\UnitTestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        static::assertEquals(
            $expected,
            (new ValidInlineHighlightedNamespaces())->check($sample->lines(), $sample->lineNumber(), 'filename')
        );
    }

    public function checkProvider(): \Generator
    {
        yield [NullViolation::create(), new RstSample('')];
        yield [NullViolation::create(), new RstSample(' ')];
        yield [NullViolation::create(), new RstSample('``AppKernel``')];
        yield [NullViolation::create(), new RstSample('`AppKernel`')];

        yield 'valid with 1 tick and 2 backslashes' => [
            NullViolation::create(),
            new RstSample('`App\\\\Entity\\\\Foo`'),
        ];

        yield 'invalid with 1 tick and 1 backslash' => [
            Violation::from(
                'Please use 2 backslashes when highlighting a namespace with single backticks: `App\Entity\Foo`',
                'filename',
                1,
                ''
            ),
            new RstSample('`App\\Entity\\Foo`'),
        ];

        yield 'valid with 2 ticks and 1 backslash' => [
            NullViolation::create(),
            new RstSample('``App\\Entity\\Foo``'),
        ];

        yield 'invalid with 2 ticks and 2 backslashes' => [
            Violation::from(
                'Please use 1 backslash when highlighting a namespace with double backticks: ``App\\\\Entity\\\\Foo``',
                'filename',
                1,
                ''
            ),
            new RstSample('``App\\\\Entity\\\\Foo``'),
        ];

        yield [NullViolation::create(), new RstSample('`int` :class:`App\\\\Entity\\\\Foo`')];
        yield [
            Violation::from(
                'Please use 2 backslashes when highlighting a namespace with single backticks: `App\Entity\Foo`',
                'filename',
                1,
                ''
            ),
            new RstSample('`int` :class:`App\\Entity\\Foo`'),
        ];

        yield [
            NullViolation::create(),
            new RstSample('the ``FormType`` object from initializing it to ``\DateTime``.'),
        ];

        yield [NullViolation::create(), new RstSample('``\Swift_Transport``')];
        yield [
            Violation::from(
                'Please use 2 backslashes when highlighting a namespace with single backticks: `\Swift_Transport`',
                'filename',
                1,
                ''
            ),
            new RstSample('`\Swift_Transport`'),
        ];

        // edge cases of the Symfony Documentation
        yield [NullViolation::create(), new RstSample('``"autoload": { "psr-4": { "SomeVendor\\BlogBundle\\": "" } }``')];
        yield [NullViolation::create(), new RstSample('look like ``@ORM\Table(name="`user`")``.')];
    }
}
