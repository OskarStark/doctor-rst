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

use App\Rule\ExtendAbstractAdmin;
use PHPUnit\Framework\TestCase;

class ExtendAbstractAdminTest extends TestCase
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
            (new ExtendAbstractAdmin())->check(new \ArrayIterator([$line]), 0)
        );
    }

    public function checkProvider()
    {
        return [
            [
                'Please extend AbstractAdmin instead of Admin',
                'class TestAdmin extends Admin',
            ],

            [
                'Please extend AbstractAdmin instead of Admin',
                '    class TestAdmin extends Admin',
            ],
            [
                null,
                'class TestAdmin extends AbstractAdmin',
            ],
            [
                null,
                '    class TestAdmin extends AbstractAdmin',
            ],
            [
                'Please use "Sonata\AdminBundle\Admin\AbstractAdmin" instead of "Sonata\AdminBundle\Admin\Admin"',
                'use Sonata\AdminBundle\Admin\Admin;',
            ],

            [
                'Please use "Sonata\AdminBundle\Admin\AbstractAdmin" instead of "Sonata\AdminBundle\Admin\Admin"',
                '    use Sonata\AdminBundle\Admin\Admin;',
            ],
            [
                null,
                'use Sonata\AdminBundle\Admin\AbstractAdmin;',
            ],
            [
                null,
                '    use Sonata\AdminBundle\Admin\AbstractAdmin;',
            ],
        ];
    }
}
