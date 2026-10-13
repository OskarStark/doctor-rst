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
            new EnsureAttributeBetweenBackticksInContent()->check($sample->lines, $sample->lineNumber, 'filename'),
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

        foreach (['diff', 'terminal', 'yaml', 'text', 'twig'] as $type) {
            yield \sprintf('No violation for code-block "%s"', $type) => [
                NullViolation::create(),
                new RstSample([
                    \sprintf('.. code-block:: %s', $type),
                    '',
                    '    #[AsEventListener]',
                ], 2),
            ];
        }

        yield 'No violation for a console output listing an attribute' => [
            NullViolation::create(),
            new RstSample([
                '.. code-block:: terminal',
                '',
                '    $ php bin/console debug:messenger',
                '',
                '      Transports',
                '      ----------',
                '',
                '      audit',
                '          App\Message\DummyQuery (from #[AsMessage])',
            ], 8),
        ];

        yield 'Has violation in content following a code-block' => [
            Violation::from(
                'Please ensure to use backticks "use #[MapEntity] attributes"',
                'filename',
                5,
                'use #[MapEntity] attributes',
            ),
            new RstSample([
                '.. code-block:: terminal',
                '',
                '    $ php bin/console debug:router',
                '',
                'use #[MapEntity] attributes',
            ], 4),
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

        yield 'Has violation in a list item following a code-block' => [
            Violation::from(
                'Please ensure to use backticks "   then use #[MapEntity] attributes"',
                'filename',
                11,
                'then use #[MapEntity] attributes',
            ),
            new RstSample([
                '.. code-block:: diff',
                '',
                '    - "symfony/symfony": "*"',
                '',
                '#. Install Flex:',
                '',
                '   .. code-block:: terminal',
                '',
                '       $ composer require symfony/flex',
                '',
                '   then use #[MapEntity] attributes',
            ], 10),
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
