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

namespace App\Tests\Helper;

use App\Helper\PhpHelper;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\Line;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class PhpHelperTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('isCommentProvider')]
    public function isComment(bool $expected, string $line): void
    {
        self::assertSame(
            $expected,
            PhpHelper::isComment(new Line($line)),
        );
    }

    /**
     * @return \Generator<array{0: bool, 1: string}>
     */
    public static function isCommentProvider(): iterable
    {
        yield [true, '# comment'];
        yield [true, '// comment'];
        yield [false, 'no comment'];
    }

    #[Test]
    #[DataProvider('containsBackslashProvider')]
    public function containsBackslash(bool $expected, string $string): void
    {
        self::assertSame($expected, PhpHelper::containsBackslash($string));
    }

    /**
     * @return \Generator<array{0: bool, 1: string}>
     */
    public static function containsBackslashProvider(): iterable
    {
        yield 'one backslash at the beginning' => [true, '\Test'];
        yield 'one backslash at the end' => [true, 'Test\\'];
        yield 'one backslash in the middle' => [true, 'Test\Test'];
        yield 'two backslashes' => [true, '\\\\Test'];

        yield 'no backslash' => [false, 'Test'];
    }

    #[Test]
    #[DataProvider('isUsingTwoBackSlashesProvider')]
    public function isUsingTwoBackslashes(bool $expected, string $string): void
    {
        self::assertSame($expected, PhpHelper::isUsingTwoBackslashes($string));
    }

    /**
     * @return \Generator<array{0: bool, 1: string}>
     */
    public static function isUsingTwoBackSlashesProvider(): iterable
    {
        yield 'two backslashes + beginning' => [true, '\\\\Test\\\\Test'];
        yield 'two backslashes' => [true, 'Test\\\\Test'];
        yield 'two backslashes 2' => [true, 'App\\\\Entity\\\\Foo'];
        yield 'no backslash' => [false, 'Test'];
        yield 'one backslashes' => [false, '\Test'];
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

    #[Test]
    #[DataProvider('isLastLineOfMultilineCommentProvider')]
    public function isLastLineOfMultilineComment(bool $expected, string $line): void
    {
        self::assertSame(
            $expected,
            PhpHelper::isLastLineOfMultilineComment(new Line($line)),
        );
    }

    /**
     * @return \Generator<array{0: bool, 1: string}>
     */
    public static function isLastLineOfMultilineCommentProvider(): iterable
    {
        yield [false, '/**'];
        yield [false, '* test'];
        yield [true, '*/'];
    }

    #[Test]
    #[DataProvider('isPartOfDocBlockProvider')]
    public function isPartOfDocBlock(bool $expected, RstSample $sample): void
    {
        self::assertSame(
            $expected,
            (new PhpHelper())->isPartOfDocBlock($sample->lines, $sample->lineNumber),
        );
    }

    /**
     * @return \Generator<array{0: bool, 1: RstSample}>
     */
    public static function isPartOfDocBlockProvider(): iterable
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

    #[Test]
    #[DataProvider('isPartOfMultilineCommentProvider')]
    public function isPartOfMultilineComment(bool $expected, RstSample $sample): void
    {
        self::assertSame(
            $expected,
            (new PhpHelper())->isPartOfMultilineComment($sample->lines, $sample->lineNumber),
        );
    }

    /**
     * @return \Generator<array{0: bool, 1: RstSample}>
     */
    public static function isPartOfMultilineCommentProvider(): iterable
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

    #[Test]
    #[DataProvider('isFirstLineOfMultilineCommentProvider')]
    public function isFirstLineOfMultilineComment(bool $expected, string $line): void
    {
        self::assertSame(
            $expected,
            PhpHelper::isFirstLineOfMultilineComment(new Line($line)),
        );
    }

    /**
     * @return \Generator<array{0: bool, 1: string}>
     */
    public static function isFirstLineOfMultilineCommentProvider(): iterable
    {
        yield [true, '/*'];
        yield [false, '/**'];
        yield [false, '* test'];
        yield [false, '*/'];
    }

    #[Test]
    #[DataProvider('isFirstLineOfDocBlockProvider')]
    public function isFirstLineOfDocBlock(bool $expected, string $line): void
    {
        self::assertSame(
            $expected,
            PhpHelper::isFirstLineOfDocBlock(new Line($line)),
        );
    }

    /**
     * @return \Generator<array{0: bool, 1: string}>
     */
    public static function isFirstLineOfDocBlockProvider(): iterable
    {
        yield [true, '/**'];
        yield [false, '/*'];
        yield [false, '* test'];
        yield [false, '*/'];
    }

    #[Test]
    #[DataProvider('isLastLineOfDocBlockProvider')]
    public function isLastLineOfDocBlock(bool $expected, string $line): void
    {
        self::assertSame(
            $expected,
            PhpHelper::isLastLineOfDocBlock(new Line($line)),
        );
    }

    /**
     * @return \Generator<array{0: bool, 1: string}>
     */
    public static function isLastLineOfDocBlockProvider(): iterable
    {
        yield [false, '/**'];
        yield [false, '* test'];
        yield [true, '*/'];
    }
}
