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

use App\Rule\EnsureAttributeBetweenBackticksInContent;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class EnsureAttributeBetweenBackticksInContentTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new EnsureAttributeBetweenBackticksInContent())->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    public static function checkProvider(): iterable
    {
        foreach (self::phpCodeBlocks() as $codeBlock) {
            yield \sprintf('No violation for code-block "%s"', $codeBlock) => [
                NullViolation::create(),
                new RstSample([
                    $codeBlock,
                    '#[AsEventListener]',
                ]),
            ];
        }

        yield 'No violation for diff code-block' => [
            NullViolation::create(),
            new RstSample([
                '.. code-block:: diff',
                '',
                '    #[AsEventListener]',
            ]),
        ];

        yield 'Has violation without backticks' => [
            Violation::from(
                'Please ensure to use backticks "use #[MapEntity] attributes"',
                'filename',
                1,
                'use #[MapEntity] attributes',
            ),
            new RstSample('use #[MapEntity] attributes'),
        ];

        yield 'Has no violation' => [
            NullViolation::create(),
            new RstSample('use ``#[MapEntity]`` attributes'),
        ];

        yield 'No violation inside :ref: directive' => [
            NullViolation::create(),
            new RstSample(':ref:`the #[Route] attribute <routing-route-attributes>`'),
        ];

        yield 'No violation inside :ref: directive with multiple attributes' => [
            NullViolation::create(),
            new RstSample(':ref:`the #[Route] and #[Entity] attributes <some-reference>`'),
        ];
    }
}
