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

use App\Rule\EnsureExplicitNullableTypes;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class EnsureExplicitNullableTypesTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('invalidProvider')]
    #[DataProvider('validProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new EnsureExplicitNullableTypes())->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    /**
     * @return \Generator<string, array{0: ViolationInterface, 1: RstSample}>
     */
    public static function validProvider(): iterable
    {
        $validCases = [
            'function foo(int $bar = 23)',
            'function foo(?int $bar = null)',
            'function foo(?\\Throwable $exception = null)',
            'function foo(\\Throwable|null $exception = null)',
            'function foo(?Foo\\Bar $bar = null)',
            'function foo(mixed $bar = null)',
            'function foo(int|null $bar = null)',
            'function foo(int|string|null $bar = null)',
        ];

        foreach ($validCases as $validCase) {
            yield $validCase => [
                NullViolation::create(),
                new RstSample([
                    '.. code-block:: php',
                    '    '.$validCase,
                ]),
            ];
        }
    }

    /**
     * @return \Generator<string, array{0: ViolationInterface, 1: RstSample}>
     */
    public static function invalidProvider(): iterable
    {
        $invalidCases = [
            'public function foo(int $bar = null)',
            'public function foo(Throwable $bar = null)',
            'public function foo(Foo\\Bar $bar = null)',
            'public function foo(\\Throwable $bar = null)',
            'public function foo(int|string $bar = null)',
            'function foo(int $foo, int $bar = null)',
            'function foo(int $foo, int $bar = null, int $baz)',
            'function foo(?int $foo = null, int $bar = null, int $baz)',
            'function foo(int|string|null $foo = null, int $bar = null, int $baz)',
            'int $foo = null,',
        ];

        foreach ($invalidCases as $invalidCase) {
            yield $invalidCase => [
                Violation::from(
                    'Please use explicit nullable types.',
                    'filename',
                    2,
                    $invalidCase,
                ),
                new RstSample([
                    '.. code-block:: php',
                    '    '.$invalidCase,
                ]),
            ];
        }
    }
}
