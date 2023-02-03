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
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;

final class NoPhpPrefixBeforeBinConsoleTest extends \App\Tests\UnitTestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        static::assertEquals(
            $expected,
            (new NoPhpPrefixBeforeBinConsole())->check($sample->lines(), $sample->lineNumber(), 'filename')
        );
    }

    public function checkProvider(): array
    {
        return [
            [
                Violation::from(
                    'Please remove "php" prefix before "bin/console"',
                    'filename',
                    1,
                    'please execute php bin/console foo'
                ),
                new RstSample('please execute php bin/console foo'),
            ],
            [
                NullViolation::create(),
                new RstSample('please execute bin/console foo'),
            ],
        ];
    }
}
