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

use App\Rst\RstParser;
use App\Rule\BlankLineAfterDirective;
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;

class BlankLineAfterDirectiveTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check(?string $expected, RstSample $sample)
    {
        $this->assertSame(
            $expected,
            (new BlankLineAfterDirective())->check($sample->lines(), $sample->lineNumber())
        );
    }

    /**
     * @return \Generator<array{0: string|null, 1: RstSample}>
     */
    public function checkProvider(): \Generator
    {
        foreach (RstParser::DIRECTIVES as $directive) {
            yield [
                null,
                new RstSample([
                    $directive,
                    '',
                    'temp',
                ]),
            ];

            $errorMessage = sprintf('Please add a blank line after "%s" directive', $directive);
            if (\in_array($directive, BlankLineAfterDirective::unSupportedDirectives())) {
                $errorMessage = null;
            }

            yield [
                $errorMessage,
                new RstSample([
                    $directive,
                    'temp',
                ]),
            ];
        }

        yield [
            null,
            new RstSample('temp'),
        ];
    }
}
