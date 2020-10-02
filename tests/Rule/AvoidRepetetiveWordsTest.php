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

use App\Rule\AvoidRepetetiveWords;
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;

class AvoidRepetetiveWordsTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     * @dataProvider whitelistProvider
     */
    public function check(?string $expected, RstSample $sample)
    {
        static::assertSame(
            $expected,
            (new AvoidRepetetiveWords())->check($sample->lines(), $sample->lineNumber())
        );
    }

    /**
     * @return \Generator<array{0: null, 1: RstSample}>
     */
    public function whitelistProvider(): \Generator
    {
        $whitelist = [
            '...',
        ];

        foreach ($whitelist as $word) {
            yield sprintf('valid whitelist: %s', $word) => [null, new RstSample(sprintf('%s %s %s', $word, $word, $word))];
        }
    }

    /**
     * @return \Generator<array{0: string|null, 1: RstSample}>
     */
    public function checkProvider()
    {
        $valid = '';
        $invalid = 'the cached items will not not be invalidated unless you clear OPcache.';

        yield 'empty' => [null, new RstSample('')];
        yield 'blank line' => [null, new RstSample('\n')];
        yield 'directive' => [null, new RstSample('.. code-block:: php')];
        yield 'link' => [null, new RstSample('.. _`Symfony`: https://symfony.com')];
        yield [null, new RstSample($valid)];

        yield [
            'The word "not" is used more times in a row.',
            new RstSample($invalid),
        ];

        yield [null, new RstSample([
            '.. code-block:: php',
            '',
            '    public function foo($bar, $bar)',
        ], 2)];
        yield 'php comment' => [
            'The word "is" is used more times in a row.',
            new RstSample([
                '.. code-block:: php',
                '',
                '    // this is is a comment',
                '    public function foo($bar, $bar)',
            ], 2),
        ];

        yield [null, new RstSample([
            '.. code-block:: xml',
            '',
            '    <xml value="1" value="1">',
        ], 2)];
        yield 'xml comment' => [
            'The word "is" is used more times in a row.',
            new RstSample([
                '.. code-block:: xml',
                '',
                '    <!-- this is is a comment -->',
                '    <xml value="1" value="1">',
            ], 2),
        ];

        yield [null, new RstSample([
            '.. code-block:: twig',
            '',
            '    {{ value }}',
        ], 2)];
        yield 'twig comment' => [
            'The word "is" is used more times in a row.',
            new RstSample([
                '.. code-block:: twig',
                '',
                '    {# this is is a comment #}',
                '    {{ value }}',
            ], 2),
        ];

        yield [null, new RstSample([
            '.. code-block:: yaml',
            '',
            '    services: ~',
        ], 2)];
        yield 'yaml comment' => [
            'The word "is" is used more times in a row.',
            new RstSample([
                '.. code-block:: yaml',
                '',
                '    # this is is a comment',
                '    services   : ~',
            ], 2),
        ];

        yield 'numeric repetition' => [
            null,
            new RstSample('This is valid 123 123'),
        ];

        yield 'numeric repetition with comma' => [
            null,
            new RstSample('224, 165, 141, 224, 164, 164, 224, 165, 135])'),
        ];
    }
}
