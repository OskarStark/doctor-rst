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

use App\Rule\NoInheritdocInCodeExamples;
use App\Tests\RstSample;

final class NoInheritdocInCodeExamplesTest extends \App\Tests\UnitTestCase
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
            (new NoInheritdocInCodeExamples())->check($sample->lines(), $sample->lineNumber())
        );
    }

    public function checkProvider(): \Generator
    {
        yield [
            null,
            new RstSample('* {@inheritdoc}'),
        ];

        yield [
            null,
            new RstSample('fine'),
        ];

        foreach (self::phpCodeBlocks() as $codeBlock) {
            yield [
                'Please do not use "@inheritdoc"',
                new RstSample([
                    $codeBlock,
                    '',
                    '    /*',
                    '     * {@inheritdoc}',
                ], 3),
            ];
        }
    }
}
