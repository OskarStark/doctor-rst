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

namespace App\Tests\Rst\Value;

use App\Rst\Value\DirectiveContent;

/**
 * @group temp
 */
final class DirectiveContentTest extends \App\Tests\UnitTestCase
{
    /**
     * @test
     */
    public function cleanedEqualsRaw(): void
    {
        $raw = [
            'foo',
            '',
            'bar',
        ];

        $content = new DirectiveContent($raw);

        static::assertSame($content->raw, $content->cleaned);
    }

    /**
     * @test
     */
    public function countWithoutBlankLines(): void
    {
        $raw = [
            'foo',
            '',
            'bar',
        ];

        $content = new DirectiveContent($raw);

        static::assertSame(3, $content->numberOfLines());
    }

    /**
     * @test
     */
    public function countWithBlankLinesAtTheBeginning(): void
    {
        $raw = [
            '',
            '',
            'foo',
            '',
            'bar',
        ];

        $content = new DirectiveContent($raw);

        static::assertSame(3, $content->numberOfLines());
    }

    /**
     * @test
     */
    public function countWithBlankLinesAtTheEnd(): void
    {
        $raw = [
            'foo',
            '',
            'bar',
            '',
            '',
        ];

        $content = new DirectiveContent($raw);

        static::assertSame(3, $content->numberOfLines());
    }
}
