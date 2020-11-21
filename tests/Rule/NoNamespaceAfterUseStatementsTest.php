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

use App\Rule\NoNamespaceAfterUseStatements;
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;

final class NoNamespaceAfterUseStatementsTest extends TestCase
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
            (new NoNamespaceAfterUseStatements())->check($sample->lines(), $sample->lineNumber())
        );
    }

    public function checkProvider(): \Generator
    {
        $codeBlocks = [
            '.. code-block:: php',
            '.. code-block:: php-annotations',
            '.. code-block:: php-attributes',
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
                    '    namespace App;',
                    '    use Symfony\A;',
                ]),
            ];

            // WITHOUT blank line after directive
            yield [
                null,
                new RstSample([
                    $codeBlock,
                    '    namespace App;',
                    '    use Symfony\A;',
                ]),
            ];
        }

        // INVALID
        foreach ($codeBlocks as $codeBlock) {
            // WITH blank line after directive
            yield [
                'Please move the namespace before the use statement(s)',
                new RstSample([
                    $codeBlock,
                    '',
                    '    use Symfony\A;',
                    '    namespace App;',
                ]),
            ];

            // WITHOUT blank line after directive
            yield [
                'Please move the namespace before the use statement(s)',
                new RstSample([
                    $codeBlock,
                    '    use Symfony\A;',
                    '    namespace App;',
                ]),
            ];
        }
    }
}
