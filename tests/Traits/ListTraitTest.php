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
     * @dataProvider inProvider
     */
    public function in(bool $expected, RstSample $sample)
    {
        $this->assertSame(
            $expected,
            $this->traitWrapper->isPartOfListItem($sample->getContent(), $sample->getLineNumber())
        );
    }

    public function inProvider()
    {
        yield [
            false,
            new RstSample(<<<'CONTENT'
I am just a cool text!
CONTENT
            ),
        ];

        $list_1 = <<<'CONTENT'
#. Line 1
   Line 2
CONTENT;

        yield 'first line (#)' => [true, new RstSample($list_1)];
        yield 'second line (#)' => [true, new RstSample($list_1, 1)];

        $list_2 = <<<'CONTENT'
* Line 1
  Line 2
CONTENT;

        yield 'first line (*)' => [true, new RstSample($list_2)];
        yield 'second line (*)' => [true, new RstSample($list_2, 1)];
    }
}
