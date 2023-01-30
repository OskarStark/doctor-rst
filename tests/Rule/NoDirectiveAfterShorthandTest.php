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

use App\Rule\NoDirectiveAfterShorthand;
use App\Tests\RstSample;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;

final class NoDirectiveAfterShorthandTest extends \App\Tests\UnitTestCase
{
    /**
     * @test
     *
     * @dataProvider validProvider
     * @dataProvider invalidProvider
     */
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        static::assertEquals(
            $expected,
            (new NoDirectiveAfterShorthand())->check($sample->lines(), $sample->lineNumber(), 'filename')
        );
    }

    /**
     * @return \Generator<string, array{0: ViolationInterface, 1: RstSample}>
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
            NullViolation::create(),
            new RstSample(''),
        ];

        yield 'valid 2' => [
            NullViolation::create(),
            new RstSample($valid2),
        ];

        yield 'valid 3' => [
            NullViolation::create(),
            new RstSample($valid3),
        ];
    }

    /**
     * @return \Generator<array{0: ViolationInterface, 1: RstSample}>
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
            Violation::from(
                'A ".. configuration-block::" directive is following a shorthand notation "::", this will lead to a broken markup!',
                'filename',
                1,
                ''
            ),
            new RstSample($invalid),
        ];

        yield [
            Violation::from(
                'A ".. configuration-block::" directive is following a shorthand notation "::", this will lead to a broken markup!',
                'filename',
                1,
                ''
            ),
            new RstSample($invalid2),
        ];
    }
}
