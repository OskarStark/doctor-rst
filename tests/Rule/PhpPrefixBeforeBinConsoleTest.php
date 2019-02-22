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

namespace app\tests\Rule;

use App\Rule\PhpPrefixBeforeBinConsole;
use PHPUnit\Framework\TestCase;

class PhpPrefixBeforeBinConsoleTest extends TestCase
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
            (new PhpPrefixBeforeBinConsole())->check(new \ArrayIterator([$line]), 0)
        );
    }

    public function checkProvider()
    {
        return [
            [
                null,
                'please execute php bin/console foo',
            ],
            [
                null,
                'you can use `bin/console` to execute',
            ],
            [
                'Please add "php" prefix before "bin/console"',
                'please execute bin/console foo',
            ],
        ];
    }
}
