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

use App\Rule\UseSingleBackticksForRefAndDoc;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class UseSingleBackticksForRefAndDocTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            new UseSingleBackticksForRefAndDoc()->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    /**
     * @return \Generator<string, array{0: ViolationInterface, 1: RstSample}>
     */
    public static function checkProvider(): iterable
    {
        yield 'closing backtick is doubled' => [
            Violation::from(
                'Please use a single backtick around "DeprecatedAlias <routing-alias-deprecation>" inside :ref: directive',
                'filename',
                1,
                ':ref:`DeprecatedAlias <routing-alias-deprecation>``',
            ),
            new RstSample(':ref:`DeprecatedAlias <routing-alias-deprecation>``'),
        ];

        yield 'opening backtick is doubled' => [
            Violation::from(
                'Please use a single backtick around "Route </routing>" inside :doc: directive',
                'filename',
                1,
                ':doc:``Route </routing>`',
            ),
            new RstSample(':doc:``Route </routing>`'),
        ];

        yield 'both backticks are doubled' => [
            Violation::from(
                'Please use a single backtick around "Route </routing>" inside :doc: directive',
                'filename',
                1,
                ':doc:``Route </routing>``',
            ),
            new RstSample(':doc:``Route </routing>``'),
        ];

        yield 'the violation is reported in the middle of a sentence' => [
            Violation::from(
                'Please use a single backtick around "Route </routing>" inside :doc: directive',
                'filename',
                1,
                'Read the :doc:`Route </routing>`` article for details.',
            ),
            new RstSample('Read the :doc:`Route </routing>`` article for details.'),
        ];

        yield 'valid :ref: directive' => [
            NullViolation::create(),
            new RstSample(':ref:`receiving them via a worker <messenger-worker>`'),
        ];

        yield 'valid :doc: directive' => [
            NullViolation::create(),
            new RstSample(':doc:`Route </routing>`'),
        ];

        yield 'valid directive next to an inline literal' => [
            NullViolation::create(),
            new RstSample('See :doc:`Route </routing>` and use ``kernel.project_dir``.'),
        ];

        yield 'the directive itself is shown as an inline literal' => [
            NullViolation::create(),
            new RstSample('Write ``:ref:`Foo``` to link to it.'),
        ];

        yield 'directive inside a code block' => [
            NullViolation::create(),
            new RstSample(<<<'RST'
                .. code-block:: rst

                    :ref:`DeprecatedAlias <routing-alias-deprecation>``
                RST, 2),
        ];
    }
}
