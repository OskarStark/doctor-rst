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

use App\Rule\UseNamedConstructorWithoutNewKeywordRule;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;

final class UseNamedConstructorWithoutNewKeywordRuleTest extends UnitTestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new UseNamedConstructorWithoutNewKeywordRule())->check($sample->lines(), $sample->lineNumber(), 'filename'),
        );
    }

    public static function checkProvider(): \Generator
    {
        foreach (self::phpCodeBlocks() as $codeBlock) {
            yield sprintf('Has violation for code-block "%s"', $codeBlock) => [
                Violation::from(
                    'Please do not use "new" keyword with named constructor',
                    'filename',
                    2,
                    $codeBlock,
                ),
                new RstSample([
                    $codeBlock,
                    '    $uuid = new Uuid::fromString("foobar");',
                ], 1),
            ];

            yield sprintf('No violation for code-block "%s"', $codeBlock) => [
                NullViolation::create(),
                new RstSample([
                    $codeBlock,
                    '    $this->somePhp();',
                    '    new class();',
                ], 2),
            ];
        }
    }
}
