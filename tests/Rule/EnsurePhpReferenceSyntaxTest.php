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

use App\Rule\EnsurePhpReferenceSyntax;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class EnsurePhpReferenceSyntaxTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new EnsurePhpReferenceSyntax())->check($sample->lines, $sample->lineNumber, 'filename'),
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

        // Test inconsistent backslash detection
        // In PHP strings: \\\\ = \\, \\ = \
        // Inconsistent: Symfony\\AI\\Platform\PlatformInterface (mix of \\ and \)
        yield 'inconsistent backslashes in class reference' => [
            Violation::from(
                'Please use consistent backslash escaping in PHP reference: `Symfony\\\\AI\\\\Platform\\PlatformInterface`',
                'filename',
                2,
                'The :class:`Symfony\\\\AI\\\\Platform\\PlatformInterface` class',
            ),
            new RstSample([
                '',
                'The :class:`Symfony\\\\AI\\\\Platform\\PlatformInterface` class',
            ], 1),
        ];

        yield 'inconsistent backslashes in method reference' => [
            Violation::from(
                'Please use consistent backslash escaping in PHP reference: `Symfony\\\\AI\\\\Platform\\PlatformInterface::invoke`',
                'filename',
                2,
                'The :method:`Symfony\\\\AI\\\\Platform\\PlatformInterface::invoke` method',
            ),
            new RstSample([
                '',
                'The :method:`Symfony\\\\AI\\\\Platform\\PlatformInterface::invoke` method',
            ], 1),
        ];

        yield 'consistent single backslashes is valid' => [
            NullViolation::create(),
            new RstSample([
                '',
                'The :class:`Symfony\\AI\\Platform\\PlatformInterface` class',
            ], 1),
        ];

        yield 'consistent double backslashes is valid' => [
            NullViolation::create(),
            new RstSample([
                '',
                'The :class:`Symfony\\\\AI\\\\Platform\\\\PlatformInterface` class',
            ], 1),
        ];

        yield 'no backslashes is valid' => [
            NullViolation::create(),
            new RstSample([
                '',
                'The :class:`PlatformInterface` class',
            ], 1),
        ];
    }
}
