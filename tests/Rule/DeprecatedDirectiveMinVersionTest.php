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

use App\Rule\DeprecatedDirectiveMinVersion;
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;

class DeprecatedDirectiveMinVersionTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check(?string $expected, string $minVersion, RstSample $sample)
    {
        $rule = (new DeprecatedDirectiveMinVersion());
        $rule->setOptions(['min_version' => $minVersion]);

        $this->assertSame($expected, $rule->check($sample->getContent(), $sample->getLineNumber()));
    }

    public function checkProvider()
    {
        return [
            [
                null,
                '3.4',
                new RstSample('.. deprecated:: 3.4'),
            ],
            [
                null,
                '3.4',
                new RstSample('.. deprecated:: 4.2'),
            ],
            [
                'Please only provide ".. deprecated::" if the version is greater/equal "3.4"',
                '3.4',
                new RstSample('.. deprecated:: 2.8'),
            ],
        ];
    }
}
