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

use App\Rule\YamlInsteadOfYmlSuffix;
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;

final class YamlInsteadOfYmlSuffixTest extends TestCase
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
            (new YamlInsteadOfYmlSuffix())->check($sample->lines(), $sample->lineNumber())
        );
    }

    /**
     * @return array<array{0: string|null, 1: RstSample}>
     */
    public function checkProvider(): array
    {
        return [
            [
                'Please use ".. code-block:: yaml" instead of ".. code-block:: yml"',
                new RstSample('.. code-block:: yml'),
            ],
            [
                null,
                new RstSample('.. code-block:: yaml'),
            ],
            [
                null,
                new RstSample('.travis.yml'),
            ],
            [
                'Please use ".yaml" instead of ".yml"',
                new RstSample('Register your service in services.yml file'),
            ],
            [
                null,
                new RstSample('Register your service in services.yaml file'),
            ],
        ];
    }
}
