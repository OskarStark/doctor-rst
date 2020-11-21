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
use App\Rule\OnlyBackslashesInNamespaceInPhpCodeBlock;
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;

final class OnlyBackslashesInNamespaceInPhpCodeBlockTest extends TestCase
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
            (new OnlyBackslashesInNamespaceInPhpCodeBlock())->check($sample->lines(), $sample->lineNumber())
        );
    }

    public function checkProvider(): \Generator
    {
        $codeBlocks = [
            RstParser::CODE_BLOCK_PHP,
            RstParser::CODE_BLOCK_PHP_ANNOTATIONS,
            RstParser::CODE_BLOCK_PHP_ATTRIBUTES,
        ];

        foreach ($codeBlocks as $codeBlock) {
            yield [
                'Please check "namespace App/Handler;", it should not contain "/"',
                new RstSample([
                    '.. code-block:: '.$codeBlock,
                    '',
                    '    // src/Handler/Collection.php',
                    '',
                    '    namespace App/Handler;',
                ], 4),
            ];

            yield [
                'Please check "NaMeSpaCe App/Handler;", it should not contain "/"',
                new RstSample([
                    '.. code-block:: '.$codeBlock,
                    '',
                    '    // src/Handler/Collection.php',
                    '',
                    '    NaMeSpaCe App/Handler;',
                ], 4),
            ];
        }

        yield [
            'Please check "namespace App/Handler;", it should not contain "/"',
            new RstSample([
                '::',
                '',
                '    // src/Handler/Collection.php',
                '',
                '    namespace App/Handler;',
            ], 4),
        ];

        yield [
            null,
            new RstSample('temp'),
        ];
    }
}
