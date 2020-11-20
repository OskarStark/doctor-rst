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

use App\Rule\NoDirectiveAfterDefaultDirective;
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;

class NoDirectiveAfterDefaultDirectiveTest extends TestCase
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
            (new NoDirectiveAfterDefaultDirective())->check($sample->lines(), $sample->lineNumber())
        );
    }

    /**
     * @return \Generator<string, array{0: null, 1: RstSample}>
     */
    public function validProvider(): \Generator
    {
        $valid2 = <<<RST
This is a sentence::

    test
    
.. code-block:: php

    test
RST;

        $valid3 = <<<RST
::

    test
    
.. code-block:: php

    test
RST;

        yield 'valid' => [
            null,
            new RstSample(''),
        ];

        yield 'valid 2' => [
            null,
            new RstSample($valid2),
        ];

        yield 'valid 3' => [
            null,
            new RstSample($valid3),
        ];
    }

    /**
     * @return \Generator<array{0: string, 1: RstSample}>
     */
    public function invalidProvider(): \Generator
    {
        $invalid = <<<RST
This is a sentence::

    .. configuration-block::
RST;

        $invalid2 = <<<RST
::

    .. configuration-block::       
RST;

        yield [
            'A ".. configuration-block::" directive is following a shorthand notation "::", this will lead to a broken markup!',
            new RstSample($invalid),
        ];

        yield [
            'A ".. configuration-block::" directive is following a shorthand notation "::", this will lead to a broken markup!',
            new RstSample($invalid2),
        ];
    }
}
