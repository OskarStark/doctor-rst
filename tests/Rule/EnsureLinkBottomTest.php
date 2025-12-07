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

use App\Rule\EnsureLinkBottom;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class EnsureLinkBottomTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new EnsureLinkBottom())->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    /**
     * @return \Generator<array{0: ViolationInterface, 1: RstSample}>
     */
    public static function checkProvider(): iterable
    {
        yield [
            NullViolation::create(),
            new RstSample('temp'),
        ];

        yield [
            NullViolation::create(),
            new RstSample([
                '',
                '.. _`first-link`: https://foo.bar',
            ], 1),
        ];

        yield [
            NullViolation::create(),
            new RstSample([
                '',
                '.. _`first-link`: https://foo.bar',
                '.. _`second-link`: https://foo.baz',
            ], 1),
        ];

        yield [
            Violation::from(
                'Please move link definition to the bottom of the page',
                'filename',
                2,
                '.. _`first-link`: https://foo.bar',
            ),
            new RstSample([
                '',
                '.. _`first-link`: https://foo.bar',
                'text after link',
            ], 1),
        ];

        yield [
            Violation::from(
                'Please move link definition to the bottom of the page',
                'filename',
                2,
                '.. _`first-link`: https://foo.bar',
            ),
            new RstSample([
                '',
                '.. _`first-link`: https://foo.bar',
                '',
                'text after link',
            ], 1),
        ];

        // Link at end of RST code block - valid
        yield 'link at end of rst code block is valid' => [
            NullViolation::create(),
            new RstSample([
                '.. code-block:: rst',
                '',
                '    Some RST content',
                '',
                '    .. _`example-link`: https://example.com',
            ], 4),
        ];

        // Multiple links at end of RST code block - valid
        yield 'multiple links at end of rst code block are valid' => [
            NullViolation::create(),
            new RstSample([
                '.. code-block:: rst',
                '',
                '    Some RST content',
                '',
                '    .. _`first-link`: https://foo.bar',
                '    .. _`second-link`: https://foo.baz',
            ], 4),
        ];

        // Link not at end of RST code block - invalid
        yield 'link not at end of rst code block is invalid' => [
            Violation::from(
                'Please move link definition to the bottom of the RST code block',
                'filename',
                4,
                '.. _`example-link`: https://example.com',
            ),
            new RstSample([
                '.. code-block:: rst',
                '',
                '    Some RST content',
                '    .. _`example-link`: https://example.com',
                '    More content after link',
            ], 3),
        ];

        // Link at end of RST code block, content continues outside - valid
        yield 'link at end of rst code block with content after code block is valid' => [
            NullViolation::create(),
            new RstSample([
                '.. code-block:: rst',
                '',
                '    Some RST content',
                '',
                '    .. _`example-link`: https://example.com',
                '',
                'Content outside code block',
            ], 4),
        ];
    }
}
