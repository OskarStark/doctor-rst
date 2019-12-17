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

use App\Rule\YarnDevOptionAtTheEnd;
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;

class YarnDevOptionAtTheEndTest extends TestCase
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
            (new YarnDevOptionAtTheEnd())->check($sample->lines(), $sample->lineNumber())
        );
    }

    public function checkProvider()
    {
        return [
            [
                'Please move "--dev" option to the end of the command',
                new RstSample('yarn add --dev jquery'),
            ],
            [
                null,
                new RstSample('yarn add jquery --dev'),
            ],
        ];
    }
}
