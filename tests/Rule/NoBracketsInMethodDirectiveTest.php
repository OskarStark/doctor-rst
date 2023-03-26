<?php

declare(strict_types=1);

/**
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
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;

final class NoBracketsInMethodDirectiveTest extends \App\Tests\UnitTestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new NoBracketsInMethodDirective())->check($sample->lines(), $sample->lineNumber(), 'filename'),
        );
    }

    /**
     * @return \Generator<array{0: ViolationInterface, 1: RstSample}>
     */
    public static function checkProvider(): \Generator
    {
        yield [
            Violation::from(
                'Please remove "()" inside :method: directive',
                'filename',
                1,
                ':method:`Symfony\\Component\\OptionsResolver\\Options::offsetGet()`',
            ),
            new RstSample(':method:`Symfony\\Component\\OptionsResolver\\Options::offsetGet()`'),
        ];

        yield [
            NullViolation::create(),
            new RstSample(':method:`Symfony\\Component\\OptionsResolver\\Options::offsetGet`'),
        ];
    }
}
