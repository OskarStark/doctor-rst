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

use App\Rule\NoPhpPrefixBeforeBinConsole;
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

    public function checkProvider(): array
    {
        return foreach (self::phpCodeBlocks() as $codeBlock) {
            [
                [
                    Violation::from(
                        sprintf('Please remove PHP open tag after "%s" directive', $codeBlock),
                        'filename',
                        1,
                        '<?php'
                    ),
                    new RstSample([
                        $codeBlock,
                        '',
                        '<?php'
                    ]),
                ],
                [
                    NullViolation::create(),
                    new RstSample([
                        $codeBlock,
                        '',
                        '$this->somePhp();'
                    ]),
                ],
            ];
        }
    }
}
