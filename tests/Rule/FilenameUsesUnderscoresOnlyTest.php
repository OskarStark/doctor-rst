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

use App\Rule\FilenameUsesUnderscoresOnly;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class FilenameUsesUnderscoresOnlyTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('invalidProvider')]
    #[DataProvider('validProvider')]
    public function check(ViolationInterface $expected, string $filename): void
    {
        $fileInfo = $this->createMock(\SplFileInfo::class);
        $fileInfo->method('getFilename')->willReturn($filename);

        $violation = (new FilenameUsesUnderscoresOnly())->check($fileInfo);
        self::assertEquals(
            $expected,
            $violation,
        );
    }

    /**
     * @return \Generator<array{0: ViolationInterface, 1: string}>
     */
    public static function validProvider(): iterable
    {
        yield [
            NullViolation::create(),
            'custom_extensions.rst',
        ];

        yield [
            NullViolation::create(),
            '_custom_extensions.rst',
        ];
    }

    /**
     * @return \Generator<array{0: ViolationInterface, 1: string}>
     */
    public static function invalidProvider(): iterable
    {
        yield [
            Violation::from(
                'Please use underscores (_) for the filename: custom-extensions.rst',
                'custom-extensions.rst',
                1,
                '',
            ),
            'custom-extensions.rst',
        ];

        yield [
            Violation::from(
                'Please use underscores (_) for the filename: _custom-extensions.rst',
                '_custom-extensions.rst',
                1,
                '',
            ),
            '_custom-extensions.rst',
        ];
    }
}
