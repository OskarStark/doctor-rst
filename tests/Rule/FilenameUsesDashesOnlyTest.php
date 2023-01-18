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

use App\Rule\FilenameUsesDashesOnly;
use PHPUnit\Framework\TestCase;

final class FilenameUsesDashesOnlyTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider validProvider
     * @dataProvider invalidProvider
     */
    public function check(?string $expected, string $filename): void
    {
        $fileInfo = $this->createMock(\SplFileInfo::class);
        $fileInfo->method('getFilename')->willReturn($filename);

        static::assertSame(
            $expected,
            (new FilenameUsesDashesOnly())->check($fileInfo)
        );
    }

    /**
     * @return \Generator<array{0: null, 1: string}>
     */
    public function validProvider(): \Generator
    {
        yield [
            null,
            'custom-extensions.rst',
        ];

        yield [
            null,
            '_custom-extensions.rst',
        ];
    }

    /**
     * @return \Generator<array{0: string, 1: string}>
     */
    public function invalidProvider(): \Generator
    {
        yield [
            'Please use underscores (_) for the filename: custom_extensions.rst',
            'custom_extensions.rst',
        ];

        yield [
            'Please use underscores (_) for the filename: _custom_extensions.rst',
            '_custom_extensions.rst',
        ];
    }
}
