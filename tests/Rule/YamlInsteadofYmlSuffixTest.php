<?php

declare(strict_types=1);

/*
 * This file is part of the rst-checker.
 *
 * (c) Oskar Stark <oskarstark@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\Rule;

use App\Rule\Sonata\FinalAdminClasses;
use App\Rule\Sonata\NoAdminYaml;
use App\Rule\Typo;
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
            (new \App\Rule\YamlInsteadOfYmlSuffix())->check(new \ArrayIterator([$line]), 0)
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
