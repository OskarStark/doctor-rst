<?php

declare(strict_types=1);

/*
 * This file is part of DOCtor-RST.
 *
 * (c) Oskar Stark <oskarstark@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\Rule;

use App\Rule\NoPhpOpenTagInCodeBlockPhpDirective;
use App\Tests\RstSample;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;

final class NoPhpOpenTagInCodeBlockPhpDirectiveTest extends \App\Tests\UnitTestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        static::assertEquals(
            $expected,
            (new NoPhpOpenTagInCodeBlockPhpDirective())->check($sample->lines(), $sample->lineNumber(), 'filename')
        );
    }

    public function checkProvider(): \Generator
    {
        foreach (self::phpCodeBlocks() as $codeBlock) {
            yield sprintf('Has violation for code-block "%s"', $codeBlock) => [
                Violation::from(
                    sprintf('Please remove PHP open tag after "%s" directive', $codeBlock),
                    'filename',
                    1,
                    $codeBlock
                ),
                new RstSample([
                    $codeBlock,
                    '',
                    '<?php',
                ]),
            ];

            yield sprintf('Has violation for code-block "%s" with comment', $codeBlock) => [
                Violation::from(
                    sprintf('Please remove PHP open tag after "%s" directive', $codeBlock),
                    'filename',
                    1,
                    $codeBlock
                ),
                new RstSample([
                    $codeBlock,
                    '',
                    '// Some comment',
                    '<?php',
                ]),
            ];

            yield sprintf('No violation for code-block "%s"', $codeBlock) => [
                NullViolation::create(),
                new RstSample([
                    $codeBlock,
                    '',
                    '$this->somePhp();',
                ]),
            ];

            yield sprintf('No violation for code-block "%s" with comment', $codeBlock) => [
                NullViolation::create(),
                new RstSample([
                    $codeBlock,
                    '',
                    '// Some comment',
                    '$this->somePhp();',
                ]),
            ];
        }
    }
}
