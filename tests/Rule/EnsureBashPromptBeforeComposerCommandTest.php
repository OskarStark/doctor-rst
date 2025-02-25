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

use App\Rst\RstParser;
use App\Rule\EnsureBashPromptBeforeComposerCommand;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class EnsureBashPromptBeforeComposerCommandTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new EnsureBashPromptBeforeComposerCommand())->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    /**
     * @return \Generator<array{0: ViolationInterface, 1: RstSample}>
     */
    public static function checkProvider(): iterable
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
                    'composer require --dev symfony/debug',
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

            yield 'more than one line of content in the directive: '.$codeBlock => [
                NullViolation::create(),
                new RstSample([
                    '.. code-block:: '.$codeBlock,
                    '',
                    '    composer require --dev symfony/debug',
                    '    composer require --dev symfony/debug',
                ], 2),
            ];
        }

        yield 'not in code-block' => [
            NullViolation::create(),
            new RstSample('composer require symfony/debug'),
        ];

        yield 'multiple uses of composer (original from the Symfony docs)' => [
            NullViolation::create(),
            new RstSample(<<<'MULTIPLE'
You can use the special ``SYMFONY_REQUIRE`` environment variable together
with Symfony Flex to install a specific Symfony version:

.. code-block:: bash

    # this requires Symfony 5.x for all Symfony packages
    export SYMFONY_REQUIRE=5.*
    # alternatively you can run this command to update composer.json config
    # composer config extra.symfony.require "5.*"

    # install Symfony Flex in the CI environment
    composer global config --no-plugins allow-plugins.symfony/flex true
    composer global require --no-progress --no-scripts --no-plugins symfony/flex

    # install the dependencies (using --prefer-dist and --no-progress is
    # recommended to have a better output and faster download time)
    composer update --prefer-dist --no-progress
MULTIPLE, 11),
        ];
    }
}
