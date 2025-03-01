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

use App\Rule\EnsureExactlyOneSpaceBeforeDirectiveType;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class EnsureExactlyOneSpaceBeforeDirectiveTypeTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('invalidProvider')]
    #[DataProvider('validProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new EnsureExactlyOneSpaceBeforeDirectiveType())->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    /**
     * @return \Generator<string, array{0: ViolationInterface, 1: RstSample}>
     */
    public static function validProvider(): iterable
    {
        $validCases = [
            '.. code-block:: php',
            '.. tip:: php',
        ];

        foreach ($validCases as $validCase) {
            yield $validCase => [
                NullViolation::create(),
                new RstSample($validCase),
            ];
        }
    }

    /**
     * @return \Generator<string, array{0: ViolationInterface, 1: RstSample}>
     */
    public static function invalidProvider(): iterable
    {
        $invalidCases = [
            '..  code-block:: php',
            '..	code-block:: php',
            '..code-block:: php',
        ];

        foreach ($invalidCases as $invalidCase) {
            yield $invalidCase => [
                Violation::from(
                    'Please use only one whitespace between ".." and the directive type.',
                    'filename',
                    1,
                    $invalidCase,
                ),
                new RstSample($invalidCase),
            ];
        }
    }
}
