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

use App\Rule\PhpPrefixBeforeBinConsole;
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;

class PhpPrefixBeforeBinConsoleTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check($expected, RstSample $sample)
    {
        $this->assertSame(
            $expected,
            (new PhpPrefixBeforeBinConsole())->check($sample->getContent(), $sample->getLineNumber())
        );
    }

    public function checkProvider()
    {
        return [
            [
                null,
                new RstSample('please execute php bin/console foo'),
            ],
            [
                null,
                new RstSample('you can use `bin/console` to execute'),
            ],
            [
                'Please add "php" prefix before "bin/console"',
                new RstSample('please execute bin/console foo'),
            ],
            [
                null,
                new RstSample('php "%s/../bin/console"'),
            ],
        ];
    }
}
