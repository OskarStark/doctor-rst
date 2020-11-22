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

use App\Rule\SpaceBetweenLabelAndLinkInDoc;
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;

final class SpaceBetweenLabelAndLinkInDocTest extends TestCase
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
            (new SpaceBetweenLabelAndLinkInDoc())->check($sample->lines(), $sample->lineNumber())
        );
    }

    /**
     * @return \Generator<array{0: string|null, 1: RstSample}>
     */
    public function checkProvider(): \Generator
    {
        yield [
            'Please add a space between "File" and "</reference/constraints/File>" inside :doc: directive',
            new RstSample(':doc:`File</reference/constraints/File>`'),
        ];

        yield [
            null,
            new RstSample(':doc:`File </reference/constraints/File>`'),
        ];
    }
}
