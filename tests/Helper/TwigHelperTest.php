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

use App\Helper\TwigHelper;
use App\Value\Line;
use PHPUnit\Framework\TestCase;

class TwigHelperTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider isCommentProvider
     */
    public function isComment(bool $expected, string $line, ?bool $closed)
    {
        static::assertSame(
            $expected,
            TwigHelper::isComment(new Line($line), $closed)
        );
    }

    public function isCommentProvider()
    {
        yield [true, '{#', null];
        yield [true, '#}', null];

        yield [true, '{# comment #}', true];
        yield [false, '{# comment #}', false];
        yield [true, '{# comment', false];
        yield [false, '{# comment', true];

        yield [false, 'no comment', null];
        yield [false, 'no comment', true];
        yield [false, 'no comment', false];
    }
}
