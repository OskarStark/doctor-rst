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

use App\Rule\BlankLineAfterColon;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class BlankLineAfterColonTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new BlankLineAfterColon())->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    /**
     * @return \Generator<array{0: ViolationInterface, 1: RstSample}>
     */
    public static function checkProvider(): iterable
    {
        yield [
            NullViolation::create(),
            new RstSample('temp'),
        ];

        yield [
            NullViolation::create(),
            new RstSample('temp:'),
        ];

        yield [
            NullViolation::create(),
            new RstSample([
                'For example:',
                '',
                '    this is a text',
            ]),
        ];

        yield [
            Violation::from(
                'Please add a blank line after "For example:"',
                'filename',
                1,
                'For example:',
            ),
            new RstSample([
                'For example:',
                'For example::',
            ]),
        ];

        yield [
            NullViolation::create(),
            new RstSample([
                '.. code-block:: yaml',
                '',
                '    session:',
                '        foo:',
            ], 2),
        ];

        yield [
            NullViolation::create(),
            new RstSample([
                '.. code-block:: yml',
                '    :option:',
                '    # config/services.yml',
                '',
                '    services:',
            ], 1),
        ];

        yield [
            NullViolation::create(),
            new RstSample([
                '',
                '.. _env-var-processors:',
                '',
                'Environment Variable Processors',
                '===============================',
                '',
            ], 1),
        ];
    }
}
