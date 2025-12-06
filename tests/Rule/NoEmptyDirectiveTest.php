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

use App\Rule\NoEmptyDirective;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

/**
 * @no-named-arguments
 */
final class NoEmptyDirectiveTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new NoEmptyDirective())
                ->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    /**
     * @return iterable<array{0: ViolationInterface, 1: RstSample}>
     */
    public static function checkProvider(): iterable
    {
        // Valid cases - directives with content
        yield 'note with content' => [
            NullViolation::create(),
            new RstSample(<<<'RST'
.. note::

    This is a note.
RST),
        ];

        yield 'caution with content' => [
            NullViolation::create(),
            new RstSample(<<<'RST'
.. caution::

    This is a caution.
RST),
        ];

        yield 'warning with content' => [
            NullViolation::create(),
            new RstSample(<<<'RST'
.. warning::

    This is a warning.
RST),
        ];

        yield 'tip with content' => [
            NullViolation::create(),
            new RstSample(<<<'RST'
.. tip::

    This is a tip.
RST),
        ];

        yield 'important with content' => [
            NullViolation::create(),
            new RstSample(<<<'RST'
.. important::

    This is important.
RST),
        ];

        yield 'seealso with content' => [
            NullViolation::create(),
            new RstSample(<<<'RST'
.. seealso::

    See also this.
RST),
        ];

        yield 'best-practice with content' => [
            NullViolation::create(),
            new RstSample(<<<'RST'
.. best-practice::

    This is a best practice.
RST),
        ];

        yield 'admonition with content' => [
            NullViolation::create(),
            new RstSample(<<<'RST'
.. admonition:: Title

    This is an admonition.
RST),
        ];

        yield 'notice with content' => [
            NullViolation::create(),
            new RstSample(<<<'RST'
.. notice::

    This is a notice.
RST),
        ];

        // Non-relevant directives should be ignored
        yield 'code-block is ignored' => [
            NullViolation::create(),
            new RstSample('.. code-block:: php'),
        ];

        yield 'image is ignored' => [
            NullViolation::create(),
            new RstSample('.. image:: /images/test.png'),
        ];

        yield 'include is ignored' => [
            NullViolation::create(),
            new RstSample('.. include:: /includes/test.rst'),
        ];

        yield 'toctree is ignored' => [
            NullViolation::create(),
            new RstSample('.. toctree::'),
        ];

        // Non-directive lines should be ignored
        yield 'plain text is ignored' => [
            NullViolation::create(),
            new RstSample('This is just plain text.'),
        ];

        yield 'blank line is ignored' => [
            NullViolation::create(),
            new RstSample(''),
        ];

        // Invalid cases - empty directives
        yield 'empty note' => [
            Violation::from(
                'The ".. note::" directive must not be empty.',
                'filename',
                1,
                '.. note::',
            ),
            new RstSample(<<<'RST'
.. note::

RST),
        ];

        yield 'empty caution' => [
            Violation::from(
                'The ".. caution::" directive must not be empty.',
                'filename',
                1,
                '.. caution::',
            ),
            new RstSample(<<<'RST'
.. caution::

RST),
        ];

        yield 'empty warning' => [
            Violation::from(
                'The ".. warning::" directive must not be empty.',
                'filename',
                1,
                '.. warning::',
            ),
            new RstSample(<<<'RST'
.. warning::

RST),
        ];

        yield 'empty tip' => [
            Violation::from(
                'The ".. tip::" directive must not be empty.',
                'filename',
                1,
                '.. tip::',
            ),
            new RstSample(<<<'RST'
.. tip::

RST),
        ];

        yield 'empty important' => [
            Violation::from(
                'The ".. important::" directive must not be empty.',
                'filename',
                1,
                '.. important::',
            ),
            new RstSample(<<<'RST'
.. important::

RST),
        ];

        yield 'empty seealso' => [
            Violation::from(
                'The ".. seealso::" directive must not be empty.',
                'filename',
                1,
                '.. seealso::',
            ),
            new RstSample(<<<'RST'
.. seealso::

RST),
        ];

        yield 'empty best-practice' => [
            Violation::from(
                'The ".. best-practice::" directive must not be empty.',
                'filename',
                1,
                '.. best-practice::',
            ),
            new RstSample(<<<'RST'
.. best-practice::

RST),
        ];

        yield 'empty notice' => [
            Violation::from(
                'The ".. notice::" directive must not be empty.',
                'filename',
                1,
                '.. notice::',
            ),
            new RstSample(<<<'RST'
.. notice::

RST),
        ];

        // Edge cases
        yield 'note with only blank lines after' => [
            Violation::from(
                'The ".. note::" directive must not be empty.',
                'filename',
                1,
                '.. note::',
            ),
            new RstSample(<<<'RST'
.. note::


RST),
        ];

        yield 'indented note with content' => [
            NullViolation::create(),
            new RstSample(<<<'RST'
    .. note::

        This is an indented note.
RST),
        ];
    }
}
