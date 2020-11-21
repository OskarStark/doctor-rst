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

use App\Rule\FilenameUsesUnderscoresOnly;
use PHPUnit\Framework\TestCase;

final class FilenameUsesUnderscoresOnlyTest extends TestCase
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
            (new FilenameUsesUnderscoresOnly())->checkFileInfo($fileInfo)
        );
    }

    /**
     * @return \Generator<array{0: null, 1: string}>
     */
    public function validProvider(): \Generator
    {
        yield [
            null,
            'custom_extensions.rst',
        ];

        yield [
            null,
            '_custom_extensions.rst',
        ];
    }

    /**
     * @return \Generator<array{0: string, 1: string}>
     */
    public function invalidProvider(): \Generator
    {
        yield [
            'Please don\'t use dashes (-) in filename: custom-extensions.rst',
            'custom-extensions.rst',
        ];

        yield [
            'Please don\'t use dashes (-) in filename: _custom-extensions.rst',
            '_custom-extensions.rst',
        ];
    }
}
