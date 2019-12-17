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
use PHPUnit\Framework\TestCase;

class ValidInlineHighlightedNamespacesTest extends TestCase
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
            (new ValidInlineHighlightedNamespaces())->check($sample->lines(), $sample->lineNumber())
        );
    }

    public function checkProvider(): \Generator
    {
        yield [null, new RstSample('')];
        yield [null, new RstSample(' ')];
        yield [null, new RstSample('``AppKernel``')];
        yield [null, new RstSample('`AppKernel`')];

        yield 'valid with 1 tick and 2 backslashes' => [
            null,
            new RstSample('`App\\\\Entity\\\\Foo`'),
        ];

        yield 'invalid with 1 tick and 1 backslash' => [
            'Please use 2 backslashes when highlighting a namespace with single backticks: `App\Entity\Foo`',
            new RstSample('`App\\Entity\\Foo`'),
        ];

        yield 'valid with 2 ticks and 1 backslash' => [
            null,
            new RstSample('``App\\Entity\\Foo``'),
        ];

        yield 'invalid with 2 ticks and 2 backslashes' => [
            'Please use 1 backslash when highlighting a namespace with double backticks: ``App\\\\Entity\\\\Foo``',
            new RstSample('``App\\\\Entity\\\\Foo``'),
        ];

        yield [null, new RstSample('`int` :class:`App\\\\Entity\\\\Foo`')];
        yield [
            'Please use 2 backslashes when highlighting a namespace with single backticks: `App\Entity\Foo`',
            new RstSample('`int` :class:`App\\Entity\\Foo`'),
        ];

        yield [
            null,
            new RstSample('the ``FormType`` object from initializing it to ``\DateTime``.'),
        ];

        yield [null, new RstSample('``\Swift_Transport``')];
        yield [
            'Please use 2 backslashes when highlighting a namespace with single backticks: `\Swift_Transport`',
            new RstSample('`\Swift_Transport`'),
        ];

        // edge cases of the Symfony Documentation
        yield [null, new RstSample('``"autoload": { "psr-4": { "SomeVendor\\BlogBundle\\": "" } }``')];
        yield [null, new RstSample('look like ``@ORM\Table(name="`user`")``.')];
    }
}
