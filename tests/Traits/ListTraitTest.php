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

namespace App\Tests\Traits;

use App\Tests\RstSample;
use App\Traits\ListTrait;
use PHPUnit\Framework\TestCase;

class ListTraitTest extends TestCase
{
    private $traitWrapper;

    protected function setUp()
    {
        $this->traitWrapper = new class() {
            use ListTrait {
                ListTrait::isPartOfListItem as public;
                ListTrait::isPartOfFootnote as public;
                ListTrait::isPartOfRstComment as public;
            }
        };
    }

    /**
     * @test
     */
    public function methodExists()
    {
        $this->assertTrue(method_exists($this->traitWrapper, 'isPartOfListItem'));
    }

    /**
     * @test
     *
     * @dataProvider listItemProvider
     */
    public function isPartOfListItem(bool $expected, RstSample $sample)
    {
        $this->assertSame(
            $expected,
            $this->traitWrapper->isPartOfListItem($sample->getContent(), $sample->getLineNumber())
        );
    }

    public function listItemProvider()
    {
        yield [
            false,
            new RstSample(<<<'RST'
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

        yield 'first line (numeric + dot + parenthesis)' => [true, new RstSample($list_5)];
        yield 'second line (numeric + dot + parenthesis)' => [true, new RstSample($list_5, 1)];

        $list_6 = <<<'RST'
1.) Line 1
    Line 2
RST;

        yield 'first line (numeric + parenthesis)' => [true, new RstSample($list_6)];
        yield 'second line (numeric + parenthesis)' => [true, new RstSample($list_6, 1)];

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
    }

    /**
     * @test
     *
     * @dataProvider footnoteProvider
     */
    public function isPartOfFootnote(bool $expected, RstSample $sample)
    {
        $this->assertSame(
            $expected,
            $this->traitWrapper->isPartOfFootnote($sample->getContent(), $sample->getLineNumber())
        );
    }

    public function footnoteProvider(): \Generator
    {
        $footnote = <<<'RST'
.. [1] Line 1
       Line 2
RST;

        yield 'first line (footnote)' => [true, new RstSample($footnote)];
        yield 'second line (footnote)' => [true, new RstSample($footnote, 1)];
    }

    /**
     * @test
     *
     * @dataProvider commentProvider
     */
    public function isPartOfRstComment(bool $expected, RstSample $sample)
    {
        $this->assertSame(
            $expected,
            $this->traitWrapper->isPartOfRstComment($sample->getContent(), $sample->getLineNumber())
        );
    }

    public function commentProvider(): \Generator
    {
        $rst_comment = <<<'RST'
.. Line 1
   Line 2
RST;

        yield 'first line (rst comment)' => [true, new RstSample($rst_comment)];
        yield 'second line (rst comment)' => [true, new RstSample($rst_comment, 1)];
    }
}
