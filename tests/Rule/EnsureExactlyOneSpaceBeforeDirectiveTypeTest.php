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

use App\Rule\EnsureExactlyOneSpaceBeforeDirectiveType;
use App\Tests\RstSample;

final class EnsureExactlyOneSpaceBeforeDirectiveTypeTest extends \App\Tests\UnitTestCase
{
    /**
     * @test
     *
     * @dataProvider validProvider
     * @dataProvider invalidProvider
     */
    public function check(?string $expected, RstSample $sample): void
    {
        static::assertSame(
            $expected,
            (new EnsureExactlyOneSpaceBeforeDirectiveType())->check($sample->lines(), $sample->lineNumber())
        );
    }

    /**
     * @return \Generator<string, array{0: null, 1: RstSample}>
     */
    public function validProvider(): \Generator
    {
        $validCases = [
            '.. code-block:: php',
            '.. tip:: php',
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
            '..  code-block:: php',
            '..	code-block:: php',
            '..code-block:: php',
        ];

        foreach ($invalidCases as $invalidCase) {
            yield $invalidCase => [
                'Please use only one whitespace between ".." and the directive type.',
                new RstSample($invalidCase),
            ];
        }
    }
}
