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

use App\Rule\LineContentRule;
use App\Rule\NoStandaloneSyntaxElement;
use App\Tests\RstSample;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class NoStandaloneSyntaxElementTest extends AbstractLineContentRuleTestCase
{
    public function createRule(): LineContentRule
    {
        $rule = new NoStandaloneSyntaxElement();
        $rule->setOptions([]);

        return $rule;
    }

    /**
     * @return \Generator<array{0: ViolationInterface, 1: RstSample}>
     */
    public static function checkProvider(): iterable
    {
        yield 'shorthand alone on a line' => [
            Violation::from(
                'Please do not use "::" alone on a line, prefer the full declaration.',
                'filename',
                3,
                '::',
            ),
            new RstSample(<<<'RST'
The following example shows the usage:

::

    $uuidFactory = new UuidFactory();
RST, 2),
        ];

        yield 'indented shorthand alone on a line' => [
            Violation::from(
                'Please do not use "::" alone on a line, prefer the full declaration.',
                'filename',
                3,
                '::',
            ),
            new RstSample(<<<'RST'
.. note::

    ::

        $uuidFactory = new UuidFactory();
RST, 2),
        ];

        yield 'shorthand attached to a sentence' => [
            NullViolation::create(),
            new RstSample(<<<'RST'
The following example shows the usage::

    $uuidFactory = new UuidFactory();
RST),
        ];

        yield 'explicit directive' => [
            NullViolation::create(),
            new RstSample(<<<'RST'
.. code-block:: php

    $uuidFactory = new UuidFactory();
RST),
        ];

        yield 'blank line' => [
            NullViolation::create(),
            new RstSample(<<<'RST'
The following example shows the usage::

    $uuidFactory = new UuidFactory();
RST, 1),
        ];

        yield 'inside a code block' => [
            NullViolation::create(),
            new RstSample(<<<'RST'
.. code-block:: rst

    ::

        $uuidFactory = new UuidFactory();
RST, 2),
        ];
    }

    #[Test]
    #[DataProvider('checkWithConfiguredElementsProvider')]
    public function checkWithConfiguredElements(ViolationInterface $expected, RstSample $sample): void
    {
        $rule = new NoStandaloneSyntaxElement();
        $rule->setOptions(['elements' => ['..']]);

        self::assertEquals(
            $expected,
            $rule->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    /**
     * @return \Generator<array{0: ViolationInterface, 1: RstSample}>
     */
    public static function checkWithConfiguredElementsProvider(): iterable
    {
        yield 'configured element alone on a line' => [
            Violation::from(
                'Please do not use ".." alone on a line, prefer the full declaration.',
                'filename',
                1,
                '..',
            ),
            new RstSample('..'),
        ];

        yield 'default element is no longer reported' => [
            NullViolation::create(),
            new RstSample(<<<'RST'
The following example shows the usage:

::

    $uuidFactory = new UuidFactory();
RST, 2),
        ];
    }
}
