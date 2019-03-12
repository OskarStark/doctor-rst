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

    public function checkProvider()
    {
        return [
            [
                'Please remove blank line after "<!-- config/services.xml -->"',
                new RstSample([
                    '.. code-block:: xml',
                    '',
                    '    <!-- config/services.xml -->',
                    '',
                    '    <foo\/>',
                ]),
            ],
            [
                null,
                new RstSample([
                    '.. code-block:: xml',
                    '',
                    '    <!-- config/services.xml -->',
                    '    <foo\/>',
                ]),
            ],
            [
                'Please remove blank line after "<!--config/services.xml-->"',
                new RstSample([
                    '.. code-block:: xml',
                    '',
                    '    <!--config/services.xml-->',
                    '',
                    '    <foo\/>',
                ]),
            ],
            [
                null,
                new RstSample([
                    '.. code-block:: xml',
                    '',
                    '    <!--config/services.xml-->',
                    '',
                    '    <!-- a comment -->',
                    '',
                    '    <foo\/>',
                ]),
            ],
            [
                null,
                new RstSample([
                    '.. code-block:: xml',
                    '',
                    '    <!--config/services.xml-->',
                    '    <foo\/>',
                ]),
            ],
            [
                null,
                new RstSample('temp'),
            ],
        ];
    }
}
