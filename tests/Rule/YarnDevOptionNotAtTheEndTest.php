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

use App\Rule\YarnDevOptionNotAtTheEnd;
use PHPUnit\Framework\TestCase;

class YarnDevOptionNotAtTheEndTest extends TestCase
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
            (new YarnDevOptionNotAtTheEnd())->check(new \ArrayIterator([$line]), 0)
        );
    }

    public function checkProvider()
    {
        return [
            [
                null,
                'yarn add --dev jquery',
            ],
            [
                'Please move "--dev" option before the package',
                'yarn add jquery --dev',
            ],
        ];
    }
}
