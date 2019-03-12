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

use App\Rule\YarnDevOptionNotAtTheEnd;
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;

class YarnDevOptionNotAtTheEndTest extends TestCase
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
            (new YarnDevOptionNotAtTheEnd())->check($sample->getContent(), $sample->getLineNumber())
        );
    }

    public function checkProvider()
    {
        return [
            [
                null,
                new RstSample('yarn add --dev jquery'),
            ],
            [
                'Please move "--dev" option before the package',
                new RstSample('yarn add jquery --dev'),
            ],
        ];
    }
}
