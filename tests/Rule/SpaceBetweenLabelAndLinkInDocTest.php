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

use App\Rule\SpaceBetweenLabelAndLinkInDoc;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class SpaceBetweenLabelAndLinkInDocTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new SpaceBetweenLabelAndLinkInDoc())->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    /**
     * @return \Generator<array{0: ViolationInterface, 1: RstSample}>
     */
    public static function checkProvider(): iterable
    {
        yield [
            Violation::from(
                'Please add a space between "File" and "</reference/constraints/File>" inside :doc: directive',
                'filename',
                1,
                ':doc:`File</reference/constraints/File>`',
            ),
            new RstSample(':doc:`File</reference/constraints/File>`'),
        ];

        yield [
            NullViolation::create(),
            new RstSample(':doc:`File </reference/constraints/File>`'),
        ];
    }
}
