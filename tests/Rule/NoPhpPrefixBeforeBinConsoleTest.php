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

use App\Rule\NoPhpPrefixBeforeBinConsole;
use App\Tests\RstSample;

final class NoPhpPrefixBeforeBinConsoleTest extends \App\Tests\UnitTestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check(?string $expected, RstSample $sample): void
    {
        static::assertSame(
            $expected,
            (new NoPhpPrefixBeforeBinConsole())->check($sample->lines(), $sample->lineNumber())
        );
    }

    public function checkProvider(): array
    {
        return [
            [
                'Please remove "php" prefix before "bin/console"',
                new RstSample('please execute php bin/console foo'),
            ],
            [
                null,
                new RstSample('please execute bin/console foo'),
            ],
        ];
    }
}
