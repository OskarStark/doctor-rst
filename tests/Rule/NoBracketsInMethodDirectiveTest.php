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

use App\Rule\NoBracketsInMethodDirective;
use App\Tests\RstSample;

final class NoBracketsInMethodDirectiveTest extends \App\Tests\UnitTestCase
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
            (new NoBracketsInMethodDirective())->check($sample->lines(), $sample->lineNumber())
        );
    }

    /**
     * @return \Generator<array{0: string|null, 1: RstSample}>
     */
    public function checkProvider(): \Generator
    {
        yield [
            'Please remove "()" inside :method: directive',
            new RstSample(':method:`Symfony\\Component\\OptionsResolver\\Options::offsetGet()`'),
        ];

        yield [
            null,
            new RstSample(':method:`Symfony\\Component\\OptionsResolver\\Options::offsetGet`'),
        ];
    }
}
