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

use App\Rst\RstParser;
use App\Rule\BlankLineBeforeDirective;
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;

final class BlankLineBeforeDirectiveTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check(?string $expected, RstSample $sample)
    {
        static::assertSame(
            $expected,
            (new BlankLineBeforeDirective())->check($sample->lines(), $sample->lineNumber())
        );
    }

    /**
     * @return \Generator<array{0: string|null, 1: RstSample}>
     */
    public function checkProvider(): \Generator
    {
        yield 'no directive' => [
            null,
            new RstSample('temp'),
        ];

        yield 'directive on the first line' => [
            null,
            new RstSample([
                '.. index::',
            ]),
        ];

        yield 'directive with ".. class::" directive before' => [
            null,
            new RstSample([
                '',
                '.. class:: foo',
                '.. code-block:: terminal',
            ], 2),
        ];

        yield 'directive with a comment directive before' => [
            null,
            new RstSample([
                '',
                '.. I am a comment',
                '.. code-block:: terminal',
            ], 2),
        ];

        yield 'valid short php directive' => [
            null,
            new RstSample(<<<'RST'
Headline

This is cool php
code::
RST
            , 3),
        ];

        foreach (RstParser::DIRECTIVES as $directive) {
            if (RstParser::DIRECTIVE_ROLE == $directive) {
                continue;
            }

            yield sprintf('valid %s', $directive) => [
                null,
                new RstSample([
                    '',
                    $directive,
                ], 1),
            ];

            yield sprintf('invalid %s', $directive) => [
                sprintf('Please add a blank line before "%s" directive', $directive),
                new RstSample([
                    'content',
                    $directive,
                ], 1),
            ];
        }
    }
}
