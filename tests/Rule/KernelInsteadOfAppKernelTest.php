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

use App\Rule\KernelInsteadOfAppKernel;
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;

class KernelInsteadOfAppKernelTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check(?string $expected, RstSample $sample)
    {
        static::assertSame(
            $expected,
            (new KernelInsteadOfAppKernel())->check($sample->lines(), $sample->lineNumber())
        );
    }

    public function checkProvider()
    {
        return [
            [
                'Please use "src/Kernel.php" instead of "app/AppKernel.php"',
                new RstSample('register the bundle in app/AppKernel.php'),
            ],
            [
                null,
                new RstSample('register the bundle in src/Kernel.php'),
            ],
            [
                'Please use "Kernel" instead of "AppKernel"',
                new RstSample('register the bundle via AppKernel'),
            ],
            [
                null,
                new RstSample('register the bundle via Kernel'),
            ],
        ];
    }
}
