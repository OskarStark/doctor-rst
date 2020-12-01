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

use App\Rule\BlankLineAfterColon;
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;

final class BlankLineAfterColonTest extends TestCase
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
            (new BlankLineAfterColon())->check($sample->lines(), $sample->lineNumber())
        );
    }

    /**
     * @return \Generator<array{0: string|null, 1: RstSample}>
     */
    public function checkProvider(): \Generator
    {
        yield [
            null,
            new RstSample('temp'),
        ];

        yield [
            null,
            new RstSample('temp:'),
        ];

        yield [
            null,
            new RstSample([
                'For example:',
                '',
                '    this is a text',
            ]),
        ];

        yield [
            'Please add a blank line after "For example:"',
            new RstSample([
                'For example:',
                'For example::',
            ]),
        ];

        yield [
            null,
            new RstSample([
                '.. code-block:: yaml',
                '',
                '    session:',
                '        foo:',
            ], 2),
        ];

        yield [
            null,
            new RstSample([
                '.. code-block:: yml',
                '    :option:',
                '    # config/services.yml',
                '',
                '    services:',
            ], 1),
        ];

        yield [
            null,
            new RstSample([
                '',
                '.. _env-var-processors:',
                '',
                'Environment Variable Processors',
                '===============================',
                '',
            ], 1),
        ];
    }
}
