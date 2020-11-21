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

final class PhpPrefixBeforeBinConsoleTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check(?string $expected, RstSample $sample)
    {
        static::assertSame(
            $expected,
            (new PhpPrefixBeforeBinConsole())->check($sample->lines(), $sample->lineNumber())
        );
    }

    public function checkProvider()
    {
        yield [null, new RstSample('please execute php bin/console foo')];
        yield [null, new RstSample('you can use `bin/console` to execute')];
        yield [null, new RstSample('php "%s/../bin/console"')];
        yield [null, new RstSample('.. _`copying Symfony\'s bin/console source`: https://github.com/symfony/recipes/blob/master/symfony/console/3.3/bin/console')];
        yield [null, new RstSample('├─ bin/console')];
        yield [null, new RstSample('Symfony\Component\Console\Application->run() at /home/greg/demo/bin/console:42')];
        yield ['Please add "php" prefix before "bin/console"', new RstSample('please execute bin/console foo')];
    }
}
