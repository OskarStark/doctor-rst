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
use App\Rule\NoInheritdocInCodeExamples;
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;

final class NoInheritdocInCodeExamplesTest extends TestCase
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

        $codeBlocks = [
            RstParser::CODE_BLOCK_PHP,
            RstParser::CODE_BLOCK_PHP_ANNOTATIONS,
            RstParser::CODE_BLOCK_PHP_ATTRIBUTES,
        ];
        foreach ($codeBlocks as $codeBlock) {
            yield [
                'Please do not use "@inheritdoc"',
                new RstSample([
                    '.. code-block:: '.$codeBlock,
                    '',
                    '    /*',
                    '     * {@inheritdoc}',
                ], 3),
            ];
        }
    }
}
