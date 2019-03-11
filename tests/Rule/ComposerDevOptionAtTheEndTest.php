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

use App\Rule\ComposerDevOptionAtTheEnd;
use PHPUnit\Framework\TestCase;

class ComposerDevOptionAtTheEndTest extends TestCase
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
            (new ComposerDevOptionAtTheEnd())->check(new \ArrayIterator([$line]), 0)
        );
    }

    public function checkProvider()
    {
        return [
            [
                'Please move "--dev" option to the end of the command',
                'composer require --dev symfony/debug',
            ],
            [
                null,
                'composer require symfony/debug --dev',
            ],
        ];
    }
}
