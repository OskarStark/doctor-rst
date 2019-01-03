<?php

declare(strict_types=1);

/*
 * This file is part of the rst-checker.
 *
 * (c) Oskar Stark <oskarstark@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\Rule;

use App\Rule\Sonata\FinalAdminClasses;
use App\Rule\Sonata\NoAdminYaml;
use App\Rule\Typo;
use PHPUnit\Framework\TestCase;

class TypoTest extends TestCase
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
            (new Typo())->check(new \ArrayIterator([$line]), 0)
        );
    }

    public function checkProvider()
    {
        return [
            [
                'Typo in word "compsoer"',
                '$ php compsoer install sonata-project/admin-bundle',
            ],
            [
                null,
                '$ php composer install sonata-project/admin-bundle',
            ],
            [
                'Typo in word "registerbundles()", use "registerBundles()"',
                'public function registerbundles()',
            ],
            [
                null,
                'public function registerBundles()',
            ],
        ];
    }
}
