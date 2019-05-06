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

class BlankLineBeforeDirectiveTest extends TestCase
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
            (new BlankLineBeforeDirective())->check($sample->getContent(), $sample->getLineNumber())
        );
    }

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

        yield 'valid short php directive' => [
            null,
            new RstSample(<<<'RST'
Headline

This is cool php
code::
RST
            , 3),
        ];

        yield 'valid because .. role directive is ignored' => [
            null,
            new RstSample(<<<'RST'
.. role:: foo
.. role:: bar
RST
                , 1),
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
