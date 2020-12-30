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

use App\Rule\ComposerDevOptionAtTheEnd;
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;

final class ComposerDevOptionAtTheEndTest extends TestCase
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
            (new ComposerDevOptionAtTheEnd())->check($sample->lines(), $sample->lineNumber())
        );
    }

    /**
     * @return \Generator<array{0: string|null, 1: RstSample}>
     */
    public function checkProvider(): \Generator
    {
        yield [
            'Please move "--dev" option to the end of the command',
            new RstSample('composer require --dev symfony/debug'),
        ];
        yield [
            null,
            new RstSample('composer require symfony/debug --dev'),
        ];
    }
}
