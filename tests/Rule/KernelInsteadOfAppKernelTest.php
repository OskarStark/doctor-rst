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

use App\Rule\KernelInsteadOfAppKernel;
use App\Rule\Sonata\FinalAdminClasses;
use App\Rule\Sonata\NoAdminYaml;
use App\Rule\Typo;
use PHPUnit\Framework\TestCase;

class KernelInsteadOfAppKernelTest extends TestCase
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
            (new KernelInsteadOfAppKernel())->check(new \ArrayIterator([$line]), 0)
        );
    }

    public function checkProvider()
    {
        return [
            [
                'Please use "src/Kernel.php" instead of "app/AppKernel.php"',
                'register the bundle in app/AppKernel.php',
            ],
            [
                null,
                'register the bundle in src/Kernel.php',
            ],
            [
                'Please use "Kernel" instead of "AppKernel"',
                'register the bundle via AppKernel',
            ],
            [
                null,
                'register the bundle via Kernel',
            ],
        ];
    }
}
