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

use App\Rule\NoPhpPrefixBeforeComposer;
use App\Tests\RstSample;

final class NoPhpPrefixBeforeComposerTest extends \App\Tests\UnitTestCase
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
            (new NoPhpPrefixBeforeComposer())->check($sample->lines(), $sample->lineNumber())
        );
    }

    public function checkProvider(): array
    {
        return [
            [
                'Please remove "php" prefix',
                new RstSample('please execute php composer'),
            ],
            [
                null,
                new RstSample('please execute composer install'),
            ],
        ];
    }
}
