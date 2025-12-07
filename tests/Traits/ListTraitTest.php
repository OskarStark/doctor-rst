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

namespace App\Tests\Traits;

use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Tests\Util\ListItemTraitWrapper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class ListTraitTest extends UnitTestCase
{
    private ListItemTraitWrapper $traitWrapper;

    protected function setUp(): void
    {
        $this->traitWrapper = new ListItemTraitWrapper();
    }

    #[Test]
    public function methodExists(): void
    {
        self::assertTrue(method_exists(ListItemTraitWrapper::class, 'isPartOfListItem'));
    }

    #[Test]
    #[DataProvider('isPartOfListItemProvider')]
    public function isPartOfListItem(bool $expected, RstSample $sample): void
    {
        self::assertSame(
            $expected,
            $this->traitWrapper->isPartOfListItem($sample->lines, $sample->lineNumber),
        );
    }

    /**
     * @return \Generator<array{0: bool, 1: RstSample}>
     */
    public static function isPartOfListItemProvider(): iterable
    {
        yield [
            false,
            new RstSample(
                <<<'RST'
I am just a cool text!
RST
            ),
        ];

        $list_1 = <<<'RST'
#. Line 1
   Line 2
RST;

        yield 'first line (#)' => [true, new RstSample($list_1)];
        yield 'second line (#)' => [true, new RstSample($list_1, 1)];

        $list_2 = <<<'RST'
* Line 1
  Line 2
RST;

        yield 'first line (*)' => [true, new RstSample($list_2)];
        yield 'second line (*)' => [true, new RstSample($list_2, 1)];

        $list_3 = <<<'RST'
A) Line 1
   Line 2
RST;

        yield 'first line (alpha uppercase)' => [true, new RstSample($list_3)];
        yield 'second line (alpha uppercase)' => [true, new RstSample($list_3, 1)];

        $list_4 = <<<'RST'
- Line 1
  Line 2
RST;

        yield 'first line (dash)' => [true, new RstSample($list_4)];
        yield 'second line (dash)' => [true, new RstSample($list_4, 1)];

        $list_5 = <<<'RST'
1. Line 1
   Line 2
RST;

        yield 'first line (numeric with dot)' => [true, new RstSample($list_5)];
        yield 'second line (numeric with dot)' => [true, new RstSample($list_5, 1)];

        $list_6 = <<<'RST'
1.) Line 1
    Line 2
RST;

        yield 'first line (numeric with dot + parenthesis)' => [true, new RstSample($list_6)];
        yield 'second line (numeric  with dot + parenthesis)' => [true, new RstSample($list_6, 1)];

        $list_7 = <<<'RST'
a) Line 1
   Line 2
RST;

        yield 'first line (alpha lowercase)' => [true, new RstSample($list_7)];
        yield 'second line (alpha lowercase)' => [true, new RstSample($list_7, 1)];

        $list_8 = <<<'RST'
1) Line 1
   Line 2
RST;

        yield 'first line (numeric + parenthesis)' => [true, new RstSample($list_8)];
        yield 'second line (numeric + parenthesis)' => [true, new RstSample($list_8, 1)];

        yield 'not list item' => [false, new RstSample(<<<'RST'
Code here::

    class User
    {
        /**
        * @Assert\NotBlank
        */
        protected $name;
    }
RST
            , 5),
        ];
    }

    #[Test]
    #[DataProvider('isPartOfFootnoteProvider')]
    public function isPartOfFootnote(bool $expected, RstSample $sample): void
    {
        self::assertSame(
            $expected,
            $this->traitWrapper->isPartOfFootnote($sample->lines, $sample->lineNumber),
        );
    }

    /**
     * @return \Generator<array{0: bool, 1: RstSample}>
     */
    public static function isPartOfFootnoteProvider(): iterable
    {
        $footnote = <<<'RST'
.. [1] Line 1
       Line 2
RST;

        yield 'first line (footnote)' => [true, new RstSample($footnote)];
        yield 'second line (footnote)' => [true, new RstSample($footnote, 1)];
    }

    #[Test]
    #[DataProvider('isPartOfRstCommentProvider')]
    public function isPartOfRstComment(bool $expected, RstSample $sample): void
    {
        self::assertSame(
            $expected,
            $this->traitWrapper->isPartOfRstComment($sample->lines, $sample->lineNumber),
        );
    }

    /**
     * @return \Generator<array{0: bool, 1: RstSample}>
     */
    public static function isPartOfRstCommentProvider(): iterable
    {
        $rst_comment = <<<'RST'
.. Line 1
   Line 2
RST;

        yield 'first line (rst comment)' => [true, new RstSample($rst_comment)];
        yield 'second line (rst comment)' => [true, new RstSample($rst_comment, 1)];
    }

    #[Test]
    #[DataProvider('isPartOfLineNumberAnnotationProvider')]
    public function isPartOfLineNumberAnnotation(bool $expected, RstSample $sample): void
    {
        self::assertSame(
            $expected,
            $this->traitWrapper->isPartOfLineNumberAnnotation($sample->lines, $sample->lineNumber),
        );
    }

    /**
     * @return \Generator<array{0: bool, 1: RstSample}>
     */
    public static function isPartOfLineNumberAnnotationProvider(): iterable
    {
        $line_number_annotation = <<<'RST'
Line 15
   Text
RST;

        yield 'first line (line number annotation)' => [true, new RstSample($line_number_annotation)];
        yield 'second line (line number annotation)' => [true, new RstSample($line_number_annotation, 1)];

        $line_number_annotation_from_to = <<<'RST'
Line 15-16
   Text
RST;

        yield 'first line (line number annotation + from/to)' => [true, new RstSample($line_number_annotation_from_to)];
        yield 'second line (line number annotation + from/to)' => [true, new RstSample($line_number_annotation_from_to, 1)];

        $line_number_annotation_from_to_spaces = <<<'RST'
Line 15 - 16
   Text
RST;

        yield 'first line (line number annotation + from/to) 2' => [true, new RstSample($line_number_annotation_from_to_spaces)];
        yield 'second line (line number annotation + from/to) 2' => [true, new RstSample($line_number_annotation_from_to_spaces, 1)];
    }
}
