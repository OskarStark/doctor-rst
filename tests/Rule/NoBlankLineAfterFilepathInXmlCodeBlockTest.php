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

use App\Rule\NoBlankLineAfterFilepathInXmlCodeBlock;
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;

class NoBlankLineAfterFilepathInXmlCodeBlockTest extends TestCase
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
            (new NoBlankLineAfterFilepathInXmlCodeBlock())->check($sample->getContent(), $sample->getLineNumber())
        );
    }

    public function checkProvider(): \Generator
    {
        $paths = [
            'config/services.xml',
            'translations/messages.xlf',
            'translations/messages.xliff',
        ];

        foreach ($paths as $path) {
            yield [
                sprintf('Please remove blank line after "<!-- %s -->"', $path),
                new RstSample([
                    '.. code-block:: xml',
                    '',
                    sprintf('    <!-- %s -->', $path),
                    '',
                    '    <foo\/>',
                ]),
            ];

            yield [
                null,
                new RstSample([
                    '.. code-block:: xml',
                    '',
                    sprintf('    <!-- %s -->',$path),
                    '    <foo\/>',
                ]),
            ];

            yield [
                sprintf('Please remove blank line after "<!--%s-->"', $path),
                new RstSample([
                    '.. code-block:: xml',
                    '',
                    sprintf('    <!--%s-->', $path),
                    '',
                    '    <foo\/>',
                ]),
            ];

            yield [
                null,
                new RstSample([
                    '.. code-block:: xml',
                    '',
                    sprintf('    <!--%s-->', $path),
                    '',
                    '    <!-- a comment -->',
                    '',
                    '    <foo\/>',
                ]),
            ];

            yield [
                null,
                new RstSample([
                    '.. code-block:: xml',
                    '',
                    sprintf('    <!--%s-->', $path),
                    '    <foo\/>',
                ]),
            ];
        }

        yield [
            null,
            new RstSample('temp'),
        ];
    }
}
