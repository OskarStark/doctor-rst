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

use App\Rule\NoDirectiveAfterShorthand;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class NoDirectiveAfterShorthandTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('invalidProvider')]
    #[DataProvider('validProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new NoDirectiveAfterShorthand())->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    /**
     * @return \Generator<string, array{0: ViolationInterface, 1: RstSample}>
     */
    public static function validProvider(): iterable
    {
        $valid2 = <<<'RST'
This is a sentence::

    test

.. code-block:: php

    test
RST;

        $valid3 = <<<'RST'
::

    test

.. code-block:: php

    test
RST;

        yield 'valid' => [
            NullViolation::create(),
            new RstSample(''),
        ];

        yield 'valid 2' => [
            NullViolation::create(),
            new RstSample($valid2),
        ];

        yield 'valid 3' => [
            NullViolation::create(),
            new RstSample($valid3),
        ];
    }

    /**
     * @return \Generator<array{0: ViolationInterface, 1: RstSample}>
     */
    public static function invalidProvider(): iterable
    {
        $invalid = <<<'RST'
This is a sentence::

    .. configuration-block::
RST;

        $invalid2 = <<<'RST'
::

    .. configuration-block::
RST;

        // https://github.com/symfony/symfony-docs/pull/21681
        $invalidNonIndentedCodeBlock = <<<'RST'
enable it::

.. code-block:: terminal

    some code
RST;

        // Multiple blank lines between shorthand and directive
        $invalidMultipleBlankLines = <<<'RST'
enable it::


.. code-block:: terminal

    some code
RST;

        yield [
            Violation::from(
                'A ".. configuration-block::" directive is following a shorthand notation "::", this will lead to a broken markup!',
                'filename',
                1,
                '.. configuration-block::',
            ),
            new RstSample($invalid),
        ];

        yield [
            Violation::from(
                'A ".. configuration-block::" directive is following a shorthand notation "::", this will lead to a broken markup!',
                'filename',
                1,
                '.. configuration-block::',
            ),
            new RstSample($invalid2),
        ];

        // Test for non-indented code-block directive following shorthand
        // This is the case from symfony/symfony-docs#21681
        yield 'non_indented_code_block_after_shorthand' => [
            Violation::from(
                'A ".. code-block:: terminal" directive is following a shorthand notation "::", this will lead to a broken markup!',
                'filename',
                1,
                '.. code-block:: terminal',
            ),
            new RstSample($invalidNonIndentedCodeBlock),
        ];

        // Test with multiple blank lines between shorthand and directive
        yield 'multiple_blank_lines_before_directive' => [
            Violation::from(
                'A ".. code-block:: terminal" directive is following a shorthand notation "::", this will lead to a broken markup!',
                'filename',
                1,
                '.. code-block:: terminal',
            ),
            new RstSample($invalidMultipleBlankLines),
        ];
    }
}

