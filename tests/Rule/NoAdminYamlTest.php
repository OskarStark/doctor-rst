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

use App\Rule\NoAdminYaml;
use PHPUnit\Framework\TestCase;

class NoAdminYamlTest extends TestCase
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
            (new NoAdminYaml())->check(new \ArrayIterator([$line]), 0)
        );
    }

    public function checkProvider()
    {
        return [
            [
                'Please use "services.yaml" instead of "admin.yml"',
                'register the admin class in admin.yml',
            ],
            [
                null,
                'register the admin class in services.yaml',
            ],
            [
                'Please use "services.yaml" instead of "admin.yaml"',
                'register the admin class in admin.yaml',
            ],
            [
                null,
                'register the admin class in services.yaml',
            ],
            [
                null,
                '# config/packages/sonata_admin.yaml',
            ],
            [
                null,
                '# config/packages/sonata_doctrine_orm_admin.yaml',
            ],
            [
                null,
                '# config/packages/sonata_doctrine_mongodb_admin.yaml',
            ],
        ];
    }
}
