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

namespace App\Tests\Rst\Value;

use App\Rst\Value\DirectiveContent;
use App\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;

#[Group('temp')]
final class DirectiveContentTest extends UnitTestCase
{
    #[Test]
    public function cleanedEqualsRaw(): void
    {
        $raw = [
            'foo',
            '',
            'bar',
        ];

        $content = new DirectiveContent($raw);

        self::assertSame($content->raw, $content->cleaned);
    }

    #[Test]
    public function countWithoutBlankLines(): void
    {
        $raw = [
            'foo',
            '',
            'bar',
        ];

        $content = new DirectiveContent($raw);

        self::assertSame(3, $content->numberOfLines());
    }

    #[Test]
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

        self::assertSame(3, $content->numberOfLines());
    }

    #[Test]
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

        self::assertSame(3, $content->numberOfLines());
    }
}
