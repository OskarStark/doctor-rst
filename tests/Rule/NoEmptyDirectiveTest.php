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
    private const array DIRECTIVES_REQUIRING_CONTENT = [
        RstParser::DIRECTIVE_NOTE,
        RstParser::DIRECTIVE_WARNING,
        RstParser::DIRECTIVE_CAUTION,
        RstParser::DIRECTIVE_TIP,
        RstParser::DIRECTIVE_IMPORTANT,
        RstParser::DIRECTIVE_SEEALSO,
        RstParser::DIRECTIVE_BEST_PRACTICE,
        RstParser::DIRECTIVE_ADMONITION,
        RstParser::DIRECTIVE_NOTICE,
    ];

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
        foreach (self::DIRECTIVES_REQUIRING_CONTENT as $directive) {
            yield \sprintf('%s with content', $directive) => [
                NullViolation::create(),
                new RstSample(\sprintf(<<<'RST'
%s

    This is content.
RST, $directive)),
            ];
        }

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
        foreach (self::DIRECTIVES_REQUIRING_CONTENT as $directive) {
            yield \sprintf('empty %s', $directive) => [
                Violation::from(
                    \sprintf('The "%s" directive must not be empty.', $directive),
                    'filename',
                    1,
                    $directive,
                ),
                new RstSample(\sprintf(<<<'RST'
%s

RST, $directive)),
            ];
        }

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
