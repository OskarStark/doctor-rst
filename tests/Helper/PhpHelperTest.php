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

namespace App\Tests\Helper;

use App\Helper\PhpHelper;
use PHPUnit\Framework\TestCase;

class PhpHelperTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider isCommentProvider
     */
    public function isComment(bool $expected, string $line)
    {
        $this->assertSame(
            $expected,
            PhpHelper::isComment($line)
        );
    }

    public function isCommentProvider()
    {
        yield [true, '# comment'];
        yield [true, '// comment'];
        yield [false, 'no comment'];
    }

    /**
     * @test
     *
     * @dataProvider containsBackslashProvider
     */
    public function containsBackslash(bool $expected, string $string)
    {
        $this->assertSame($expected, PhpHelper::containsBackslash($string));
    }

    public function containsBackslashProvider(): \Generator
    {
        yield 'one backslash at the beginning' => [true, '\Test'];
        yield 'one backslash at the end' => [true, 'Test\\'];
        yield 'one backslash in the middle' => [true, 'Test\Test'];
        yield 'two backslashes' => [true, '\\\\Test'];

        yield 'no backslash' => [false, 'Test'];
    }

    /**
     * @test
     *
     * @dataProvider isStartingWithOneBackslashProvider
     */
    public function isStartingWithOneBackslash(bool $expected, string $string)
    {
        $this->assertSame($expected, PhpHelper::isStartingWithOneBackslash($string));
    }

    public function isStartingWithOneBackslashProvider(): \Generator
    {
        yield 'one backslash' => [true, '\Test'];
        yield 'no backslash' => [false, 'Test'];
        yield 'two backslashes' => [false, '\\\\Test'];
    }

    /**
     * @test
     *
     * @dataProvider isStartingWithTwoBackslashesProvider
     */
    public function isStartingWithTwoBackslashes(bool $expected, string $string)
    {
        $this->assertSame($expected, PhpHelper::isStartingWithTwoBackslashes($string));
    }

    public function isStartingWithTwoBackslashesProvider(): \Generator
    {
        yield 'one backslash' => [false, '\Test'];
        yield 'no backslash' => [false, 'Test'];
        yield 'two backslashes' => [true, '\\\\Test'];
    }

    /**
     * @test
     *
     * @dataProvider isUsingOneBackslashProvider
     */
    public function isUsingOneBackslash(bool $expected, string $string)
    {
        $this->assertSame($expected, PhpHelper::isUsingOneBackslash($string));
    }

    public function isUsingOneBackslashProvider(): \Generator
    {
        yield 'one backslash + beginning' => [true, '\Test\Test'];
        yield 'one backslash' => [true, 'Test\Test'];
        yield 'no backslash' => [false, 'Test'];
        yield 'two backslashes' => [false, '\\\\Test'];
    }

    /**
     * @test
     *
     * @dataProvider isUsingTwoBackSlashesProvider
     */
    public function isUsingTwoBackslashes(bool $expected, string $string)
    {
        $this->assertSame($expected, PhpHelper::isUsingTwoBackslashes($string));
    }

    public function isUsingTwoBackSlashesProvider(): \Generator
    {
        yield 'two backslashes + beginning' => [true, '\\\\Test\\\\Test'];
        yield 'two backslashes' => [true, 'Test\\\\Test'];
        yield 'two backslashes 2' => [true, 'App\\\\Entity\\\\Foo'];
        yield 'no backslash' => [false, 'Test'];
        yield 'one backslashes' => [false, '\Test'];
    }
}
