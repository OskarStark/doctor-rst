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

use App\Rule\EnsureShorthandColons;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class EnsureShorthandColonsTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new EnsureShorthandColons())->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    /**
     * @return \Generator<array{0: ViolationInterface, 1: RstSample}>
     */
    public static function checkProvider(): iterable
    {
        // Valid: line with :: followed by code block
        yield 'valid_shorthand_with_code_block' => [
            NullViolation::create(),
            new RstSample(<<<'RST'
use it as follows::

    $uuidFactory = new UuidFactory();
RST),
        ];

        // Invalid: line with : followed by code block (missing ::)
        yield 'invalid_single_colon_with_code_block' => [
            Violation::from(
                'Please use "::" (shorthand) to introduce a code block.',
                'filename',
                1,
                'use it as follows:',
            ),
            new RstSample(<<<'RST'
use it as follows:

    $uuidFactory = new UuidFactory();
RST),
        ];

        // Valid: line with : not followed by indented block
        yield 'valid_colon_without_indented_block' => [
            NullViolation::create(),
            new RstSample(<<<'RST'
This is a sentence:

This is not indented.
RST),
        ];

        // Valid: blank line
        yield 'valid_blank_line' => [
            NullViolation::create(),
            new RstSample(''),
        ];

        // Valid: line without colon
        yield 'valid_no_colon' => [
            NullViolation::create(),
            new RstSample('This is a sentence'),
        ];

        // Valid: line with :: at end
        yield 'valid_double_colon' => [
            NullViolation::create(),
            new RstSample('This is a sentence::'),
        ];

        // Valid: directive option
        yield 'valid_directive_option' => [
            NullViolation::create(),
            new RstSample(':linenos:'),
        ];

        // Valid: list item
        yield 'valid_list_item' => [
            NullViolation::create(),
            new RstSample('* This is a list item:'),
        ];

        // Valid: RST anchor
        yield 'valid_anchor' => [
            NullViolation::create(),
            new RstSample('.. _my-anchor:'),
        ];

        // Valid: link definition
        yield 'valid_link_definition' => [
            NullViolation::create(),
            new RstSample('.. _`link`: https://example.com'),
        ];

        // Valid: directive line
        yield 'valid_directive' => [
            NullViolation::create(),
            new RstSample('.. code-block:: php'),
        ];

        // Valid: colon followed by non-blank line
        yield 'valid_colon_followed_by_content' => [
            NullViolation::create(),
            new RstSample(<<<'RST'
This is a sentence:
Not indented, direct continuation.
RST),
        ];

        // Valid: colon at end of file
        yield 'valid_colon_at_end' => [
            NullViolation::create(),
            new RstSample('This is a sentence:'),
        ];

        // Invalid: another example with indented block
        yield 'invalid_example_with_class' => [
            Violation::from(
                'Please use "::" (shorthand) to introduce a code block.',
                'filename',
                1,
                'Here is an example:',
            ),
            new RstSample(<<<'RST'
Here is an example:

    class Foo
    {
    }
RST),
        ];

        // Valid: inside YAML code block
        yield 'valid_inside_yaml_code_block' => [
            NullViolation::create(),
            new RstSample([
                '.. code-block:: yaml',
                '',
                '    services:',
                '        app.service:',
            ], 3),
        ];

        // Valid: next line is a directive
        yield 'valid_followed_by_directive' => [
            NullViolation::create(),
            new RstSample(<<<'RST'
Consider the following:

    .. note::

        This is a note.
RST),
        ];

        // Valid: multiple blank lines before indented content with ::
        yield 'valid_multiple_blank_lines' => [
            NullViolation::create(),
            new RstSample(<<<'RST'
Here is an example::


    $foo = 'bar';
RST),
        ];
    }
}
