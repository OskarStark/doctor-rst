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

use App\Rule\NoBlankLineAfterFilepathInYamlCodeBlock;
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;

class NoBlankLineAfterFilepathInYamlCodeBlockTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check(?string $expected, RstSample $sample)
    {
        $this->assertSame(
            $expected,
            (new NoBlankLineAfterFilepathInYamlCodeBlock())->check($sample->lines(), $sample->lineNumber())
        );
    }

    public function checkProvider()
    {
        return [
            [
                'Please remove blank line after "# config/services.yml"',
                new RstSample([
                    '.. code-block:: yml',
                    '',
                    '    # config/services.yml',
                    '',
                    '    services:',
                ]),
            ],
            [
                null,
                new RstSample([
                    '.. code-block:: yml',
                    '',
                    '    # config/services.yml',
                    '    services:',
                ]),
            ],
            [
                'Please remove blank line after "# config/services.yaml"',
                new RstSample([
                    '.. code-block:: yaml',
                    '',
                    '    # config/services.yaml',
                    '',
                    '    services:',
                ]),
            ],
            [
                null,
                new RstSample([
                    '.. code-block:: yaml',
                    '',
                    '    # config/services.yaml',
                    '',
                    '    # a comment',
                    '    services:',
                ]),
            ],
            [
                null,
                new RstSample([
                    '.. code-block:: yaml',
                    '',
                    '    # config/services.yaml',
                    '    services:',
                ]),
            ],
            [
                null,
                new RstSample('temp'),
            ],
        ];
    }
}
