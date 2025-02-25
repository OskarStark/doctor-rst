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

use App\Rule\BlankLineAfterFilepathInTwigCodeBlock;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class BlankLineAfterFilepathInTwigCodeBlockTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new BlankLineAfterFilepathInTwigCodeBlock())->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    /**
     * @return \Generator<array{0: ViolationInterface, 1: RstSample}>
     */
    public static function checkProvider(): iterable
    {
        yield [
            Violation::from(
                'Please add a blank line after "{# templates/index.html.twig #}"',
                'filename',
                3,
                '{# templates/index.html.twig #}',
            ),
            new RstSample([
                '.. code-block:: twig',
                '',
                '    {# templates/index.html.twig #}',
                '    {% set foo = "bar" %}',
            ]),
        ];
        yield [
            NullViolation::create(),
            new RstSample([
                '.. code-block:: twig',
                '',
                '    {# templates/index.html.twig #}',
                '',
                '    {% set foo = "bar" %}',
            ]),
        ];
        yield [
            Violation::from(
                'Please add a blank line after "{# templates/index.html.twig #}"',
                'filename',
                3,
                '{# templates/index.html.twig #}',
            ),
            new RstSample([
                '.. code-block:: jinja',
                '',
                '    {# templates/index.html.twig #}',
                '    {% set foo = "bar" %}',
            ]),
        ];
        yield [
            NullViolation::create(),
            new RstSample([
                '.. code-block:: jinja',
                '',
                '    {# templates/index.html.twig #}',
                '',
                '    {% set foo = "bar" %}',
            ]),
        ];
        yield [
            Violation::from(
                'Please add a blank line after "{# templates/index.html.twig #}"',
                'filename',
                3,
                '{# templates/index.html.twig #}',
            ),
            new RstSample([
                '.. code-block:: html+jinja',
                '',
                '    {# templates/index.html.twig #}',
                '    {% set foo = "bar" %}',
            ]),
        ];
        yield [
            NullViolation::create(),
            new RstSample([
                '.. code-block:: html+jinja',
                '',
                '    {# templates/index.html.twig #}',
                '',
                '    {% set foo = "bar" %}',
            ]),
        ];
        yield [
            Violation::from(
                'Please add a blank line after "{# templates/index.html.twig #}"',
                'filename',
                3,
                '{# templates/index.html.twig #}',
            ),
            new RstSample([
                '.. code-block:: html+twig',
                '',
                '    {# templates/index.html.twig #}',
                '    {% set foo = "bar" %}',
            ]),
        ];
        yield [
            NullViolation::create(),
            new RstSample([
                '.. code-block:: html+twig',
                '',
                '    {# templates/index.html.twig #}',
                '    {# a comment #}',
                '    {% set foo = "bar" %}',
            ]),
        ];
        yield [
            NullViolation::create(),
            new RstSample([
                '.. code-block:: html+twig',
                '',
                '    {# templates/index.html.twig #}',
                '',
                '    {% set foo = "bar" %}',
            ]),
        ];
        yield [
            NullViolation::create(),
            new RstSample('temp'),
        ];
    }
}
