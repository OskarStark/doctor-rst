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
use App\Rule\EnsureBashPromptBeforeComposerCommand;
use App\Tests\RstSample;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;

final class EnsureBashPromptBeforeComposerCommandTest extends \App\Tests\UnitTestCase
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
            (new EnsureBashPromptBeforeComposerCommand())->check($sample->lines(), $sample->lineNumber(), 'filename')
        );
    }

    /**
     * @return \Generator<array{0: ViolationInterface, 1: RstSample}>
     */
    public function checkProvider(): \Generator
    {
        $codeBlocks = [
            RstParser::CODE_BLOCK_BASH,
            RstParser::CODE_BLOCK_SHELL,
            RstParser::CODE_BLOCK_TERMINAL,
        ];

        foreach ($codeBlocks as $codeBlock) {
            yield [
                Violation::from(
                    'Please add a bash prompt "$" before composer command',
                    'filename',
                    3,
                    'composer require --dev symfony/debug'
                ),
                new RstSample([
                    '.. code-block:: '.$codeBlock,
                    '',
                    '    composer require --dev symfony/debug',
                ], 2),
            ];

            yield [
                NullViolation::create(),
                new RstSample([
                    '.. code-block:: '.$codeBlock,
                    '',
                    '    $ composer require --dev symfony/debug',
                ], 2),
        ];
        }

        yield 'not in code-block' => [
            NullViolation::create(),
            new RstSample('composer require symfony/debug'),
        ];
    }
}
