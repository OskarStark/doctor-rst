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
use App\Tests\RstSample;

final class ExtendAbstractAdminTest extends \App\Tests\UnitTestCase
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
            (new ExtendAbstractAdmin())->check($sample->lines(), $sample->lineNumber())
        );
    }

    public function checkProvider(): array
    {
        return [
            [
                'Please extend AbstractAdmin instead of Admin',
                new RstSample('class TestAdmin extends Admin'),
            ],

            [
                'Please extend AbstractAdmin instead of Admin',
                new RstSample('    class TestAdmin extends Admin'),
            ],
            [
                null,
                new RstSample('class TestAdmin extends AbstractAdmin'),
            ],
            [
                null,
                new RstSample('    class TestAdmin extends AbstractAdmin'),
            ],
            [
                'Please use "Sonata\AdminBundle\Admin\AbstractAdmin" instead of "Sonata\AdminBundle\Admin\Admin"',
                new RstSample('use Sonata\AdminBundle\Admin\Admin;'),
            ],
            [
                'Please use "Sonata\AdminBundle\Admin\AbstractAdmin" instead of "Sonata\AdminBundle\Admin\Admin"',
                new RstSample('    use Sonata\AdminBundle\Admin\Admin;'),
            ],
            [
                null,
                new RstSample('use Sonata\AdminBundle\Admin\AbstractAdmin;'),
            ],
            [
                null,
                new RstSample('    use Sonata\AdminBundle\Admin\AbstractAdmin;'),
            ],
        ];
    }
}
