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
use App\Rule\ArgumentVariableMustMatchType;
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;

final class ArgumentVariableMustMatchTypeTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check(?string $expected, RstSample $sample): void
    {
        $rule = new ArgumentVariableMustMatchType();
        $rule->setOptions([
            'arguments' => [
                [
                    'type' => 'ContainerBuilder',
                    'name' => 'containerBuilder',
                ],
                [
                    'type' => 'ContainerConfigurator',
                    'name' => 'containerConfigurator',
                ],
            ],
        ]);

        static::assertSame(
            $expected,
            $rule->check($sample->lines(), $sample->lineNumber())
        );
    }

    /**
     * @return \Generator<array{0: string|null, 1: RstSample}>
     */
    public function checkProvider(): \Generator
    {
        $codeBlocks = [
            RstParser::CODE_BLOCK_PHP,
            RstParser::CODE_BLOCK_PHP_ANNOTATIONS,
            RstParser::CODE_BLOCK_PHP_ATTRIBUTES,
        ];

        foreach ($codeBlocks as $codeBlock) {
            yield [
                'Please rename "$builder" to "$containerBuilder"',
                new RstSample([
                    '.. code-block:: '.$codeBlock,
                    '',
                    'public function loadExtension(array $config, ContainerConfigurator $containerConfigurator, ContainerBuilder $builder): void',
                ]),
            ];

            yield [
                'Please rename "$builder" to "$containerBuilder". Please rename "$configurator" to "$containerConfigurator"',
                new RstSample([
                    '.. code-block:: '.$codeBlock,
                    '',
                    'public function loadExtension(array $config, ContainerConfigurator $configurator, ContainerBuilder $builder): void',
                ]),
            ];

            yield [
                null,
                new RstSample([
                    '.. code-block:: '.$codeBlock,
                    '',
                    'public function loadExtension(array $config, ContainerConfigurator $containerConfigurator, ContainerBuilder $containerBuilder): void',
                ]),
            ];

            yield [
                'Please rename "$configurator" to "$containerConfigurator"',
                new RstSample([
                    '.. code-block:: '.$codeBlock,
                    '',
                    'ContainerConfigurator $configurator',
                ]),
            ];

            yield [
                null,
                new RstSample([
                    '.. code-block:: '.$codeBlock,
                    '',
                    'ContainerConfigurator $containerConfigurator',
                ]),
            ];
        }

        yield [
            null,
            new RstSample('temp'),
        ];
    }

    /**
     * @test
     */
    public function invalidOptionType(): void
    {
        $this->expectExceptionObject(
            new InvalidOptionsException('The nested option "arguments" with value "foo" is expected to be of type array, but is of type "string".')
        );

        $rule = new ArgumentVariableMustMatchType();
        $rule->setOptions([
            'arguments' => 'foo',
        ]);
    }

    /**
     * @test
     */
    public function invalidOptionValue(): void
    {
        $this->expectExceptionObject(
            new UndefinedOptionsException('The option "arguments[0][foo]" does not exist. Defined options are: "name", "type".')
        );

        $rule = new ArgumentVariableMustMatchType();
        $rule->setOptions([
            'arguments' => [
                [
                    'foo' => 'bar',
                ],
            ],
        ]);
    }
}
