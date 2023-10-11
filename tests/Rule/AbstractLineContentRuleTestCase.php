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
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\ViolationInterface;

abstract class AbstractLineContentRuleTestCase extends UnitTestCase
{
    abstract public function createRule(): LineContentRule;

    /**
     * @return \Generator<array{0: ViolationInterface, 1: RstSample}>
     */
    abstract public static function checkProvider(): iterable;

    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            static::createRule()->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }
}
