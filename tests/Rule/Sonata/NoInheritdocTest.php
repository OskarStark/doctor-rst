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

namespace app\tests\Rule\Sonata;

use App\Rule\Sonata\NoInheritdoc;
use PHPUnit\Framework\TestCase;

class NoInheritdocTest extends TestCase
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
            (new NoInheritdoc())->check(new \ArrayIterator([$line]), 0)
        );
    }

    public function checkProvider()
    {
        return [
            [
                'Please do not use "@inheritdoc"',
                '* {@inheritdoc}',
            ],
            [
                null,
                'fine',
            ],
        ];
    }
}
