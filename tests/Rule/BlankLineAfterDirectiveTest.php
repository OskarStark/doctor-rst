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
use App\Rule\BlankLineAfterDirective;
use App\Tests\RstSample;

final class BlankLineAfterDirectiveTest extends \App\Tests\UnitTestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check(?string $expected, RstSample $sample): void
    {
        static::assertSame(
            $expected,
            (new BlankLineAfterDirective())->check($sample->lines(), $sample->lineNumber())
        );
    }

    /**
     * @return \Generator<array{0: string|null, 1: RstSample}>
     */
    public function checkProvider(): \Generator
    {
        foreach (RstParser::DIRECTIVES as $directive) {
            yield [
                null,
                new RstSample([
                    $directive,
                    '',
                    'temp',
                ]),
            ];

            yield [
                null,
                new RstSample([
                    $directive,
                    ':lineos:',
                    '',
                    'temp',
                ]),
            ];

            $errorMessage = sprintf('Please add a blank line after "%s" directive', $directive);
            if (\in_array($directive, BlankLineAfterDirective::unSupportedDirectives(), true)) {
                $errorMessage = null;
            }

            yield [
                $errorMessage,
                new RstSample([
                    $directive,
                    'temp',
                ]),
            ];

            yield [
                $errorMessage,
                new RstSample([
                    $directive,
                ]),
            ];
        }

        yield [
            null,
            new RstSample(<<<SAMPLE
.. code-block:: text
    :caption: src/app.js
    :emphasize-lines: 3,11

    import {h, render} from 'preact';
SAMPLE
),
        ];

        yield [
            null,
            new RstSample('temp'),
        ];
    }
}
