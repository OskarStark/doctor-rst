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

use App\Tests\UnitTestCase;
use App\Rule\EnsurePhpReferenceSyntax;
use App\Tests\RstSample;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;

final class EnsurePhpReferenceSyntaxTest extends UnitTestCase
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
            (new EnsurePhpReferenceSyntax())->check($sample->lines(), $sample->lineNumber(), 'filename'),
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
            new RstSample([
                '',
                'The :class:`Symfony\\Component\\Notifier\\Transport` class',
            ], 1),
        ];

        yield [
            Violation::from(
                'Please use one backtick at the end of the reference',
                'filename',
                2,
                'The :class:`Symfony\\Component\\Notifier\\Transport`` class',
            ),
            new RstSample([
                '',
                'The :class:`Symfony\\Component\\Notifier\\Transport`` class',
            ], 1),
        ];

        yield [
            NullViolation::create(),
            new RstSample([
                '',
                'The :method:`Symfony\\Bundle\\FrameworkBundle\\Controller\\AbstractController::createNotFoundException`',
            ], 1),
        ];

        yield [
            Violation::from(
                'Please use one backtick at the end of the reference',
                'filename',
                2,
                'The :method:`Symfony\\Bundle\\FrameworkBundle\\Controller\\AbstractController::createNotFoundException``',
            ),
            new RstSample([
                '',
                'The :method:`Symfony\\Bundle\\FrameworkBundle\\Controller\\AbstractController::createNotFoundException``',
            ], 1),
        ];
    }
}
