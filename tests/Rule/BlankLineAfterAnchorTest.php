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

use App\Rule\BlankLineAfterAnchor;
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;

final class BlankLineAfterAnchorTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check(?string $expected, RstSample $sample): void
    {
        static::assertSame(
            $expected,
            (new BlankLineAfterAnchor())->check($sample->lines(), $sample->lineNumber())
        );
    }

    /**
     * @return \Generator<array{0: string|null, 1: RstSample}>
     */
    public function checkProvider(): \Generator
    {
        yield [
            null,
            new RstSample('temp'),
        ];

        yield [
            null,
            new RstSample([
                '',
                '.. _env-var-processors:',
                '',
                'Environment Variable Processors',
                '===============================',
                '',
            ], 1),
        ];

        yield [
            null,
            new RstSample([
                '.. _env-var-processors:',
                '',
                'Environment Variable Processors',
                '===============================',
                '',
            ]),
        ];

        yield [
            null,
            new RstSample([
                '',
                '.. _env-var-processors:',
                '.. _`special-env-var-processors`:',
                '',
                'Environment Variable Processors',
                '===============================',
                '',
            ], 1),
        ];

        yield [
            null,
            new RstSample([
                '',
                '.. _env-var-processors:',
                '.. _`special-env-var-processors`:',
                '.. _`super-special-env-var-processors`:',
                '',
                'Environment Variable Processors',
                '===============================',
                '',
            ], 1),
        ];

        yield [
            null,
            new RstSample([
                '.. index::',
                '    single: Deployment; Deployment tools',
                '',
                '.. _how-to-deploy-a-symfony2-application:',
                '',
                'How to Deploy a Symfony Application',
                '===================================',
                '',
            ], 3),
        ];

        yield [
            null,
            new RstSample([
                '.. index::',
                '    single: Deployment; Deployment tools',
                '',
                '.. _`how-to-deploy-a-symfony2-application`:',
                '',
                'How to Deploy a Symfony Application',
                '===================================',
                '',
            ], 3),
        ];
    }
}
