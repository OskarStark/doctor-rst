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

use App\Rule\EnsureExactlyOneSpaceBetweenLinkDefinitionAndLink;
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;

class EnsureExactlyOneSpaceBetweenLinkDefinitionAndLinkTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider validProvider
     * @dataProvider invalidProvider
     */
    public function check(?string $expected, RstSample $sample)
    {
        static::assertSame(
            $expected,
            (new EnsureExactlyOneSpaceBetweenLinkDefinitionAndLink())->check($sample->lines(), $sample->lineNumber())
        );
    }

    /**
     * @return \Generator<string, array{0: null, 1: RstSample}>
     */
    public function validProvider(): \Generator
    {
        $validCases = [
            '.. _DOCtor-RST: https://github.com/OskarStark/DOCtor-RST',
            '.. _`DOCtor-RST`: https://github.com/OskarStark/DOCtor-RST',
            '.. _`use DOCtor-RST`: https://github.com/OskarStark/DOCtor-RST',
        ];

        foreach ($validCases as $validCase) {
            yield $validCase => [
                null,
                new RstSample($validCase),
            ];
        }
    }

    /**
     * @return \Generator<string, array{0: string, 1: RstSample}>
     */
    public function invalidProvider(): \Generator
    {
        $invalidCases = [
            '.. _DOCtor-RST:  https://github.com/OskarStark/DOCtor-RST',
            '.. _`DOCtor-RST`:  https://github.com/OskarStark/DOCtor-RST',
            '.. _`use DOCtor-RST`:  https://github.com/OskarStark/DOCtor-RST',
        ];

        foreach ($invalidCases as $invalidCase) {
            yield $invalidCase => [
                'Please use only one whitespace between the link definition and the link.',
                new RstSample($invalidCase),
            ];
        }
    }
}
