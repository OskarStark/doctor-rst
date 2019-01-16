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

use App\Rule\Sonata\FinalAdminClasses;
use PHPUnit\Framework\TestCase;

class FinalAdminClassesTest extends TestCase
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
            (new FinalAdminClasses())->check(new \ArrayIterator([$line]), 0)
        );
    }

    public function checkProvider()
    {
        return [
            [
                'Please use "final" for Admin class',
                'class TestAdmin extends AbstractAdmin',
            ],

            [
                'Please use "final" for Admin class',
                '    class TestAdmin extends AbstractAdmin',
            ],
            [
                null,
                'final class TestAdmin extends AbstractAdmin',
            ],
        ];
    }
}
