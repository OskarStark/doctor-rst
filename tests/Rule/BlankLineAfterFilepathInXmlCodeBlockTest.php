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

use App\Rule\BlankLineAfterFilepathInXmlCodeBlock;
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;

class BlankLineAfterFilepathInXmlCodeBlockTest extends TestCase
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
            (new BlankLineAfterFilepathInXmlCodeBlock())->check($sample->getContent(), $sample->getLineNumber())
        );
    }

    public function checkProvider()
    {
        return [
            [
                'Please add a blank line after "<!-- config/services.xml -->"',
                new RstSample([
                    '.. code-block:: xml',
                    '',
                    '    <!-- config/services.xml -->',
                    '    <foo\/>',
                ]),
            ],
            [
                null,
                new RstSample([
                    '.. code-block:: xml',
                    '',
                    '    <!-- config/services.xml -->',
                    '',
                    '    <foo\/>',
                ]),
            ],
            [
                'Please add a blank line after "<!--config/services.xml-->"',
                new RstSample([
                    '.. code-block:: xml',
                    '',
                    '    <!--config/services.xml-->',
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
