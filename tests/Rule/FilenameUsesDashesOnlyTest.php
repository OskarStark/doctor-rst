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

use App\Rule\FilenameUsesDashesOnly;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class FilenameUsesDashesOnlyTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('invalidProvider')]
    #[DataProvider('validProvider')]
    public function check(ViolationInterface $expected, string $filename): void
    {
        $fileInfo = $this->createMock(\SplFileInfo::class);
        $fileInfo->method('getFilename')->willReturn($filename);

        self::assertEquals(
            $expected,
            (new FilenameUsesDashesOnly())->check($fileInfo),
        );
    }

    /**
     * @return \Generator<array{0: ViolationInterface, 1: string}>
     */
    public static function validProvider(): iterable
    {
        yield [
            NullViolation::create(),
            'custom-extensions.rst',
        ];

        yield [
            NullViolation::create(),
            '_custom-extensions.rst',
        ];
    }

    /**
     * @return \Generator<array{0: ViolationInterface, 1: string}>
     */
    public static function invalidProvider(): iterable
    {
        yield [
            Violation::from(
                'Please use dashes (-) for the filename: custom_extensions.rst',
                'custom_extensions.rst',
                1,
                '',
            ),
            'custom_extensions.rst',
        ];

        yield [
            Violation::from(
                'Please use dashes (-) for the filename: _custom_extensions.rst',
                '_custom_extensions.rst',
                1,
                '',
            ),
            '_custom_extensions.rst',
        ];
    }
}
