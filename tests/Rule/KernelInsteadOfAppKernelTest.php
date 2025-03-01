<?php

declare(strict_types=1);

/**
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
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class KernelInsteadOfAppKernelTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new KernelInsteadOfAppKernel())->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    public static function checkProvider(): iterable
    {
        yield from [
            [
                Violation::from(
                    'Please use "src/Kernel.php" instead of "app/AppKernel.php"',
                    'filename',
                    1,
                    'register the bundle in app/AppKernel.php',
                ),
                new RstSample('register the bundle in app/AppKernel.php'),
            ],
            [
                Violation::from(
                    'Please use "src/Kernel.php" instead of "app/AppKernel.php"',
                    'filename',
                    1,
                    'register the bundle in app/AppKernel.php',
                ),
                new RstSample('    register the bundle in app/AppKernel.php'),
            ],
            [
                NullViolation::create(),
                new RstSample('register the bundle in src/Kernel.php'),
            ],
            [
                Violation::from(
                    'Please use "Kernel" instead of "AppKernel"',
                    'filename',
                    1,
                    'register the bundle via AppKernel',
                ),
                new RstSample('register the bundle via AppKernel'),
            ],
            [
                Violation::from(
                    'Please use "Kernel" instead of "AppKernel"',
                    'filename',
                    1,
                    'register the bundle via AppKernel',
                ),
                new RstSample('    register the bundle via AppKernel'),
            ],
            [
                NullViolation::create(),
                new RstSample('register the bundle via Kernel'),
            ],
        ];
    }
}
