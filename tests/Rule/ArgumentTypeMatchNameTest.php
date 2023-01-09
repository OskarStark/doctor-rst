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

use App\Rule\ArgumentTypeMatchName;
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;

final class ArgumentTypeMatchNameTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check(?string $expected, RstSample $sample): void
    {
        $rule = new ArgumentTypeMatchName();
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
     * @return array<array{0: string|null, 1: RstSample}>
     */
    public function checkProvider(): array
    {
        return [
            [
                'Please name the argument "ContainerBuilder $containerBuilder"',
                new RstSample('public function loadExtension(array $config, ContainerConfigurator $containerConfigurator, ContainerBuilder $builder): void'),
            ],
            [
                'Please name the argument "ContainerBuilder $containerBuilder". Please name the argument "ContainerConfigurator $containerConfigurator"',
                new RstSample('public function loadExtension(array $config, ContainerConfigurator $configurator, ContainerBuilder $builder): void'),
            ],
            [
                null,
                new RstSample('public function loadExtension(array $config, ContainerConfigurator $containerConfigurator, ContainerBuilder $containerBuilder): void'),
            ],
            [
                'Please name the argument "ContainerConfigurator $containerConfigurator"',
                new RstSample('ContainerConfigurator $configurator'),
            ],
            [
                null,
                new RstSample('ContainerConfigurator $containerConfigurator'),
            ],
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

        $rule = new ArgumentTypeMatchName();
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

        $rule = new ArgumentTypeMatchName();
        $rule->setOptions([
            'arguments' => [
                [
                    'foo' => 'bar',
                ],
            ],
        ]);
    }
}
