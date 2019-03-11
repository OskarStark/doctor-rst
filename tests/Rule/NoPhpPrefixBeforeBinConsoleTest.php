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
use PHPUnit\Framework\TestCase;

class NoPhpPrefixBeforeBinConsoleTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check($expected, $line)
    {
        $this->assertSame(
            $expected,
            (new NoPhpPrefixBeforeBinConsole())->check(new \ArrayIterator([$line]), 0)
        );
    }

    public function checkProvider()
    {
        return [
            [
                'Please remove "php" prefix before "bin/console"',
                'please execute php bin/console foo',
            ],
            [
                null,
                'please execute bin/console foo',
            ],
        ];
    }
}
