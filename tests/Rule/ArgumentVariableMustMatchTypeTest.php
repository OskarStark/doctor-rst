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

use App\Rule\ArgumentVariableMustMatchType;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;

final class ArgumentVariableMustMatchTypeTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
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

        self::assertEquals(
            $expected,
            $rule->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    /**
     * @return \Generator<array{0: ViolationInterface, 1: RstSample}>
     */
    public static function checkProvider(): iterable
    {
        foreach (self::phpCodeBlocks() as $codeBlock) {
            yield [
                Violation::from(
                    'Please rename "$builder" to "$containerBuilder"',
                    'filename',
                    3,
                    'public function loadExtension(array $config, ContainerConfigurator $containerConfigurator, ContainerBuilder $builder): void',
                ),
                new RstSample([
                    $codeBlock,
                    '',
                    '    public function loadExtension(array $config, ContainerConfigurator $containerConfigurator, ContainerBuilder $builder): void',
                ], 2),
            ];

            yield [
                Violation::from(
                    'Please rename "$builder" to "$containerBuilder". Please rename "$configurator" to "$containerConfigurator"',
                    'filename',
                    3,
                    'public function loadExtension(array $config, ContainerConfigurator $configurator, ContainerBuilder $builder): void',
                ),
                new RstSample([
                    $codeBlock,
                    '',
                    '    public function loadExtension(array $config, ContainerConfigurator $configurator, ContainerBuilder $builder): void',
                ], 2),
            ];

            yield [
                NullViolation::create(),
                new RstSample([
                    $codeBlock,
                    '',
                    '    public function loadExtension(array $config, ContainerConfigurator $containerConfigurator, ContainerBuilder $containerBuilder): void',
                ]),
            ];

            yield [
                Violation::from(
                    'Please rename "$configurator" to "$containerConfigurator"',
                    'filename',
                    3,
                    'ContainerConfigurator $configurator',
                ),
                new RstSample([
                    $codeBlock,
                    '',
                    '    ContainerConfigurator $configurator',
                ], 2),
            ];

            yield [
                NullViolation::create(),
                new RstSample([
                    $codeBlock,
                    '',
                    '    ContainerConfigurator $containerConfigurator',
                ]),
            ];

            yield [
                Violation::from(
                    'Please rename "$configurator" to "$containerConfigurator"',
                    'filename',
                    6,
                    'ContainerConfigurator $configurator',
                ),
                new RstSample([
                    $codeBlock,
                    '    some',
                    '    text',
                    '    before',
                    '    violation',
                    '    ContainerConfigurator $configurator',
                    '    some',
                    '    text',
                    '    after',
                ], 5),
            ];
        }

        yield [
            NullViolation::create(),
            new RstSample('temp'),
        ];
    }

    #[Test]
    public function invalidOptionType(): void
    {
        $this->expectExceptionObject(
            new InvalidOptionsException('The nested option "arguments" with value "foo" is expected to be of type array, but is of type "string".'),
        );

        $rule = new ArgumentVariableMustMatchType();
        $rule->setOptions([
            'arguments' => 'foo',
        ]);
    }

    #[Test]
    public function invalidOptionValue(): void
    {
        $this->expectExceptionObject(
            new UndefinedOptionsException('The option "arguments[0][foo]" does not exist. Defined options are: "name", "type".'),
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
