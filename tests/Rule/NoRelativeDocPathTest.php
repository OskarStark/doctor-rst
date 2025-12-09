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

use App\Rule\NoRelativeDocPath;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class NoRelativeDocPathTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new NoRelativeDocPath())->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    /**
     * @return \Generator<array{0: ViolationInterface, 1: RstSample}>
     */
    public static function checkProvider(): iterable
    {
        // Invalid: relative path without label
        yield 'relative path without label' => [
            Violation::from(
                'Please use an absolute path for :doc: directive, e.g., :doc:`/maintenance`',
                'filename',
                1,
                ':doc:`maintenance`',
            ),
            new RstSample(':doc:`maintenance`'),
        ];

        // Invalid: relative path with label
        yield 'relative path with label' => [
            Violation::from(
                'Please use an absolute path for :doc: directive, e.g., :doc:`/maintenance`',
                'filename',
                1,
                ':doc:`File <maintenance>`',
            ),
            new RstSample(':doc:`File <maintenance>`'),
        ];

        // Invalid: relative path with nested path
        yield 'relative nested path without label' => [
            Violation::from(
                'Please use an absolute path for :doc: directive, e.g., :doc:`/contributing/code/maintenance`',
                'filename',
                1,
                ':doc:`contributing/code/maintenance`',
            ),
            new RstSample(':doc:`contributing/code/maintenance`'),
        ];

        // Invalid: relative path with nested path and label
        yield 'relative nested path with label' => [
            Violation::from(
                'Please use an absolute path for :doc: directive, e.g., :doc:`/contributing/code/maintenance`',
                'filename',
                1,
                ':doc:`Maintenance <contributing/code/maintenance>`',
            ),
            new RstSample(':doc:`Maintenance <contributing/code/maintenance>`'),
        ];

        // Valid: absolute path without label
        yield 'absolute path without label' => [
            NullViolation::create(),
            new RstSample(':doc:`/contributing/code/maintenance`'),
        ];

        // Valid: absolute path with label
        yield 'absolute path with label' => [
            NullViolation::create(),
            new RstSample(':doc:`Maintenance </contributing/code/maintenance>`'),
        ];

        // Valid: no :doc: directive
        yield 'no doc directive' => [
            NullViolation::create(),
            new RstSample('Some regular text without doc directive'),
        ];

        // Valid: other directives should not be affected
        yield 'ref directive not affected' => [
            NullViolation::create(),
            new RstSample(':ref:`relative-reference`'),
        ];

        // Valid: inline text in paragraph
        yield 'doc directive in paragraph' => [
            NullViolation::create(),
            new RstSample('Read the :doc:`/contributing/code/maintenance` documentation.'),
        ];

        // Invalid: inline relative path in paragraph
        yield 'relative doc in paragraph' => [
            Violation::from(
                'Please use an absolute path for :doc: directive, e.g., :doc:`/maintenance`',
                'filename',
                1,
                'Read the :doc:`maintenance` documentation.',
            ),
            new RstSample('Read the :doc:`maintenance` documentation.'),
        ];
    }
}
