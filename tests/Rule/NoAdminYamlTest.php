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

use App\Rule\NoAdminYaml;
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;

class NoAdminYamlTest extends TestCase
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
            (new NoAdminYaml())->check($sample->getContent(), $sample->getLineNumber())
        );
    }

    public function checkProvider()
    {
        return [
            [
                'Please use "services.yaml" instead of "admin.yml"',
                new RstSample('register the admin class in admin.yml'),
            ],
            [
                null,
                new RstSample('register the admin class in services.yaml'),
            ],
            [
                'Please use "services.yaml" instead of "admin.yaml"',
                new RstSample('register the admin class in admin.yaml'),
            ],
            [
                null,
                new RstSample('register the admin class in services.yaml'),
            ],
            [
                null,
                new RstSample('# config/packages/sonata_admin.yaml'),
            ],
            [
                null,
                new RstSample('# config/packages/sonata_doctrine_orm_admin.yaml'),
            ],
            [
                null,
                new RstSample('# config/packages/sonata_doctrine_mongodb_admin.yaml'),
            ],
        ];
    }
}
