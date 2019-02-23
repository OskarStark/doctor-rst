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

namespace app\tests\Rule;

use App\Rule\NoBlankLineAfterFilepathInYamlCodeBlock;
use PHPUnit\Framework\TestCase;

class NoBlankLineAfterFilepathInYamlCodeBlockTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check($expected, $line)
    {
        $this->assertSame(
            $expected,
            (new NoBlankLineAfterFilepathInYamlCodeBlock())->check(new \ArrayIterator(\is_array($line) ? $line : [$line]), 0)
        );
    }

    public function checkProvider()
    {
        return [
            [
                'Please remove blank line after "# config/services.yml"',
                [
                    '.. code-block:: yml',
                    '',
                    '# config/services.yml',
                    '',
                    'services:',
                ],
            ],
            [
                null,
                [
                    '.. code-block:: yml',
                    '',
                    '# config/services.yml',
                    'services:',
                ],
            ],
            [
                'Please remove blank line after "# config/services.yaml"',
                [
                    '.. code-block:: yaml',
                    '',
                    '# config/services.yaml',
                    '',
                    'services:',
                ],
            ],
            [
                null,
                [
                    '.. code-block:: yaml',
                    '',
                    '# config/services.yaml',
                    'services:',
                ],
            ],
            [
                null,
                'temp',
            ],
        ];
    }
}
