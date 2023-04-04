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

use App\Rule\ForbiddenDirectives;
use App\Tests\RstSample;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

final class ForbiddenDirectivesTest extends \App\Tests\UnitTestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        $rule = new ForbiddenDirectives();
        $rule->setOptions([
            'directives' => [
                '.. index::',
                '.. caution::',
            ],
        ]);

        self::assertEquals(
            $expected,
            $rule->check($sample->lines(), $sample->lineNumber(), 'filename'),
        );
    }

    /**
     * @return \Generator<array{0: ViolationInterface, 1: RstSample}>
     */
    public static function checkProvider(): \Generator
    {
        yield [
            Violation::from(
                'Please don\'t use directive ".. index::" anymore',
                'filename',
                1,
                '.. index::',
            ),
            new RstSample([
                '.. index::',
            ]),
        ];

        yield [
            Violation::from(
                'Please don\'t use directive ".. caution::" anymore',
                'filename',
                1,
                '.. caution::',
            ),
            new RstSample([
                '.. caution::',
            ]),
        ];

        yield [
            NullViolation::create(),
            new RstSample([
                '.. tip::',
            ]),
        ];

        yield [
            NullViolation::create(),
            new RstSample('temp'),
        ];
    }

    /**
     * @test
     */
    public function invalidOptionType(): void
    {
        $this->expectExceptionObject(
            new InvalidOptionsException('The option "directives" with value ".. caution::" is expected to be of type "string[]", but is of type "string".'),
        );

        $rule = new ForbiddenDirectives();
        $rule->setOptions([
            'directives' => '.. caution::',
        ]);
    }

    /**
     * @test
     */
    public function checkWithNoConfiguration(): void
    {
        $rule = new ForbiddenDirectives();
        $rule->setOptions([]);

        $sample = new RstSample('temp');
        self::assertEquals(
            NullViolation::create(),
            $rule->check($sample->lines(), $sample->lineNumber(), 'filename'),
        );
    }
}
