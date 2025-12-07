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

use App\Rst\RstParser;
use App\Rule\BlankLineAfterDirective;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class BlankLineAfterDirectiveTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new BlankLineAfterDirective())->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    /**
     * @return \Generator<array{0: ViolationInterface, 1: RstSample}>
     */
    public static function checkProvider(): iterable
    {
        foreach (RstParser::DIRECTIVES as $directive) {
            yield [
                NullViolation::create(),
                new RstSample([
                    $directive,
                    '',
                    'temp',
                ]),
            ];

            yield [
                NullViolation::create(),
                new RstSample([
                    $directive,
                    ':lineos:',
                    '',
                    'temp',
                ]),
            ];

            $violation = Violation::from(
                \sprintf('Please add a blank line after "%s" directive', $directive),
                'filename',
                1,
                $directive,
            );

            if (\in_array($directive, BlankLineAfterDirective::unSupportedDirectives(), true)) {
                $violation = NullViolation::create();
            }

            yield [
                $violation,
                new RstSample([
                    $directive,
                    'temp',
                ]),
            ];

            yield [
                $violation,
                new RstSample([
                    $directive,
                ]),
            ];
        }

        yield [
            NullViolation::create(),
            new RstSample(
                <<<'SAMPLE'
.. code-block:: text
    :caption: src/app.js
    :emphasize-lines: 3,11

    import {h, render} from 'preact';
SAMPLE
            ),
        ];

        yield [
            NullViolation::create(),
            new RstSample('temp'),
        ];
    }
}
