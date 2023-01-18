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
use App\Rule\LowercaseAsInUseStatements;
use App\Tests\RstSample;

final class LowercaseAsInUseStatementTest extends \App\Tests\UnitTestCase
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
            (new LowercaseAsInUseStatements())->check($sample->lines(), $sample->lineNumber())
        );
    }

    public function checkProvider(): \Generator
    {
        $codeBlocks = [
            '.. code-block:: '.RstParser::CODE_BLOCK_PHP,
            '.. code-block:: '.RstParser::CODE_BLOCK_PHP_ANNOTATIONS,
            '.. code-block:: '.RstParser::CODE_BLOCK_PHP_ATTRIBUTES,
            '.. code-block:: '.RstParser::CODE_BLOCK_PHP_SYMFONY,
            '.. code-block:: '.RstParser::CODE_BLOCK_PHP_STANDALONE,
            'A php code block follows::',
        ];

        // VALID
        foreach ($codeBlocks as $codeBlock) {
            // WITH blank line after directive
            yield [
                null,
                new RstSample([
                    $codeBlock,
                    '',
                    '    use Symfony\A as A;',
                ]),
            ];

            // WITHOUT blank line after directive
            yield [
                null,
                new RstSample([
                    $codeBlock,
                    '    use Symfony\A as A;',
                ]),
            ];
        }

        // INVALID
        foreach ($codeBlocks as $codeBlock) {
            foreach (['AS', 'As', 'aS'] as $invalid) {
                // WITH blank line after directive
                yield [
                    sprintf('Please use lowercase "as" instead of "%s"', $invalid),
                    new RstSample([
                        $codeBlock,
                        '',
                        sprintf('    use Symfony\A %s A;', $invalid),
                    ]),
                ];

                // WITHOUT blank line after directive
                yield [
                    sprintf('Please use lowercase "as" instead of "%s"', $invalid),
                    new RstSample([
                        $codeBlock,
                        sprintf('    use Symfony\A %s A;', $invalid),
                    ]),
                ];
            }
        }
    }
}
