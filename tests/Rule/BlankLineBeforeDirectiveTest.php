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
use App\Rule\BlankLineBeforeDirective;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class BlankLineBeforeDirectiveTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new BlankLineBeforeDirective())->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    /**
     * @return \Generator<array{0: ViolationInterface, 1: RstSample}>
     */
    public static function checkProvider(): iterable
    {
        yield 'no directive' => [
            NullViolation::create(),
            new RstSample('temp'),
        ];

        yield 'directive on the first line' => [
            NullViolation::create(),
            new RstSample([
                '.. index::',
            ]),
        ];

        yield 'directive with ".. class::" directive before' => [
            NullViolation::create(),
            new RstSample([
                '',
                '.. class:: foo',
                '.. code-block:: terminal',
            ], 2),
        ];

        yield 'directive with a comment directive before' => [
            NullViolation::create(),
            new RstSample([
                '',
                '.. I am a comment',
                '.. code-block:: terminal',
            ], 2),
        ];

        yield 'valid short php directive' => [
            NullViolation::create(),
            new RstSample(<<<'RST'
Headline

This is cool php
code::
RST
                , 3),
        ];

        foreach (RstParser::DIRECTIVES as $directive) {
            if (RstParser::DIRECTIVE_ROLE === $directive) {
                continue;
            }

            yield \sprintf('valid %s', $directive) => [
                NullViolation::create(),
                new RstSample([
                    '',
                    $directive,
                ], 1),
            ];

            yield \sprintf('invalid %s', $directive) => [
                Violation::from(
                    \sprintf('Please add a blank line before "%s" directive', $directive),
                    'filename',
                    2,
                    $directive,
                ),
                new RstSample([
                    'content',
                    $directive,
                ], 1),
            ];
        }
    }
}
