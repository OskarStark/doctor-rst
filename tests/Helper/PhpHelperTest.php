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
use App\Tests\RstSample;
use App\Value\Line;
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
        static::assertSame(
            $expected,
            PhpHelper::isComment(new Line($line))
        );
    }

    /**
     * @return \Generator<array{0: bool, 1: string}>
     */
    public function isCommentProvider(): \Generator
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
        static::assertSame($expected, PhpHelper::containsBackslash($string));
    }

    /**
     * @return \Generator<array{0: bool, 1: string}>
     */
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
     * @dataProvider isUsingTwoBackSlashesProvider
     */
    public function isUsingTwoBackslashes(bool $expected, string $string)
    {
        static::assertSame($expected, PhpHelper::isUsingTwoBackslashes($string));
    }

    /**
     * @return \Generator<array{0: bool, 1: string}>
     */
    public function isUsingTwoBackSlashesProvider(): \Generator
    {
        yield 'two backslashes + beginning' => [true, '\\\\Test\\\\Test'];
        yield 'two backslashes' => [true, 'Test\\\\Test'];
        yield 'two backslashes 2' => [true, 'App\\\\Entity\\\\Foo'];
        yield 'no backslash' => [false, 'Test'];
        yield 'one backslashes' => [false, '\Test'];
    }

    /**
     * @test
     *
     * @dataProvider isUsingOneBackslashProvider
     */
    public function isUsingOneBackslash(bool $expected, string $string)
    {
        static::assertSame($expected, PhpHelper::isUsingOneBackslash($string));
    }

    /**
     * @return \Generator<array{0: bool, 1: string}>
     */
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
     * @dataProvider isStartingWithOneBackslashProvider
     */
    public function isStartingWithOneBackslash(bool $expected, string $string)
    {
        static::assertSame($expected, PhpHelper::isStartingWithOneBackslash($string));
    }

    /**
     * @return \Generator<array{0: bool, 1: string}>
     */
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
        static::assertSame($expected, PhpHelper::isStartingWithTwoBackslashes($string));
    }

    /**
     * @return \Generator<array{0: bool, 1: string}>
     */
    public function isStartingWithTwoBackslashesProvider(): \Generator
    {
        yield 'one backslash' => [false, '\Test'];
        yield 'no backslash' => [false, 'Test'];
        yield 'two backslashes' => [true, '\\\\Test'];
    }

    /**
     * @test
     * @dataProvider isLastLineOfMultilineCommentProvider
     */
    public function isLastLineOfMultilineComment(bool $expected, string $line)
    {
        static::assertSame(
            $expected,
            PhpHelper::isLastLineOfMultilineComment(new Line($line))
        );
    }

    /**
     * @return \Generator<array{0: bool, 1: string}>
     */
    public function isLastLineOfMultilineCommentProvider(): \Generator
    {
        yield [false, '/**'];
        yield [false, '* test'];
        yield [true, '*/'];
    }

    /**
     * @test
     *
     * @dataProvider isPartOfDocBlockProvider
     */
    public function isPartOfDocBlock(bool $expected, RstSample $sample)
    {
        static::assertSame(
            $expected,
            (new PhpHelper())->isPartOfDocBlock($sample->lines(), $sample->lineNumber())
        );
    }

    /**
     * @return \Generator<array{0: bool, 1: RstSample}>
     */
    public function isPartOfDocBlockProvider(): \Generator
    {
        $valid = <<<'RST'
class User
{
    /**
     * @Assert\NotBlank
     */
    protected $name;
}
RST;

        yield 'first line' => [true, new RstSample($valid, 2)];
        yield 'second line' => [true, new RstSample($valid, 3)];
        yield 'last line' => [true, new RstSample($valid, 4)];
        yield 'not part of doc block' => [false, new RstSample($valid, 5)];
    }

    /**
     * @test
     *
     * @dataProvider isPartOfMultilineCommentProvider
     */
    public function isPartOfMultilineComment(bool $expected, RstSample $sample)
    {
        static::assertSame(
            $expected,
            (new PhpHelper())->isPartOfMultilineComment($sample->lines(), $sample->lineNumber())
        );
    }

    /**
     * @return \Generator<array{0: bool, 1: RstSample}>
     */
    public function isPartOfMultilineCommentProvider(): \Generator
    {
        $valid = <<<'RST'
    /*
     * this is a nice variable!
     */
    $var = 'foo';
RST;

        yield 'first line' => [true, new RstSample($valid, 0)];
        yield 'second line' => [true, new RstSample($valid, 1)];
        yield 'last line' => [true, new RstSample($valid, 2)];
        yield 'not part of multiline comment' => [false, new RstSample($valid, 3)];

        $valid = <<<'RST'
    /*
        Example Result
    */
    $var = 'foo';
RST;

        yield 'no asterisk - first line' => [true, new RstSample($valid, 0)];
        yield 'no asterisk - second line' => [true, new RstSample($valid, 1)];
        yield 'no asterisk - last line' => [true, new RstSample($valid, 2)];
        yield 'no asterisk - not part of multiline comment' => [false, new RstSample($valid, 3)];
    }

    /**
     * @test
     *
     * @dataProvider isFirstLineOfMultilineCommentProvider
     */
    public function isFirstLineOfMultilineComment(bool $expected, string $line)
    {
        static::assertSame(
            $expected,
            PhpHelper::isFirstLineOfMultilineComment(new Line($line))
        );
    }

    /**
     * @return \Generator<array{0: bool, 1: string}>
     */
    public function isFirstLineOfMultilineCommentProvider(): \Generator
    {
        yield [true, '/*'];
        yield [false, '/**'];
        yield [false, '* test'];
        yield [false, '*/'];
    }

    /**
     * @test
     *
     * @dataProvider isFirstLineOfDocBlockProvider
     */
    public function isFirstLineOfDocBlock(bool $expected, string $line)
    {
        static::assertSame(
            $expected,
            PhpHelper::isFirstLineOfDocBlock(new Line($line))
        );
    }

    /**
     * @return \Generator<array{0: bool, 1: string}>
     */
    public function isFirstLineOfDocBlockProvider(): \Generator
    {
        yield [true, '/**'];
        yield [false, '/*'];
        yield [false, '* test'];
        yield [false, '*/'];
    }

    /**
     * @test
     *
     * @dataProvider isLastLineOfDocBlockProvider
     */
    public function isLastLineOfDocBlock(bool $expected, string $line)
    {
        static::assertSame(
            $expected,
            PhpHelper::isLastLineOfDocBlock(new Line($line))
        );
    }

    /**
     * @return \Generator<array{0: bool, 1: string}>
     */
    public function isLastLineOfDocBlockProvider(): \Generator
    {
        yield [false, '/**'];
        yield [false, '* test'];
        yield [true, '*/'];
    }
}
