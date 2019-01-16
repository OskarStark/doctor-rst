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

use App\Rule\YamlInsteadOfYmlSuffix;
use PHPUnit\Framework\TestCase;

class YamlInsteadofYmlSuffixTest extends TestCase
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
            (new YamlInsteadOfYmlSuffix())->check(new \ArrayIterator([$line]), 0)
        );
    }

    public function checkProvider()
    {
        return [
            [
                'Please use ".. code-block:: yaml" instead of ".. code-block:: yml"',
                '.. code-block:: yml',
            ],
            [
                null,
                '.. code-block:: yaml',
            ],
            [
                'Please use ".yaml" instead of ".yml"',
                'Register your service in services.yml file',
            ],
            [
                null,
                'Register your service in services.yaml file',
            ],
        ];
    }
}
