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

use App\Rule\FinalAdminClasses;
use App\Tests\RstSample;
use App\Value\NullViolation;
use App\Value\RuleName;
use App\Value\Violation;
use App\Value\ViolationInterface;

final class FinalAdminClassesTest extends \App\Tests\UnitTestCase
{
    /**
     * @test
     */
    public function name(): void
    {
        static::assertInstanceOf(RuleName::class, FinalAdminClasses::getName());
        static::assertSame('final_admin_classes', FinalAdminClasses::getName()->toString());
    }

    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        static::assertEquals(
            $expected,
            (new FinalAdminClasses())->check($sample->lines(), $sample->lineNumber(), 'filename')
        );
    }

    /**
     * @return array<array{0: ViolationInterface, 1: RstSample}>
     */
    public function checkProvider(): array
    {
        return [
            [
                Violation::from(
                    'Please use "final" for Admin class',
                    'filename',
                    1,
                    'class TestAdmin extends AbstractAdmin'
                ),
                new RstSample('class TestAdmin extends AbstractAdmin'),
            ],

            [
                Violation::from(
                    'Please use "final" for Admin class',
                    'filename',
                    1,
                    'class TestAdmin extends AbstractAdmin'
                ),
                new RstSample('    class TestAdmin extends AbstractAdmin'),
            ],
            [
                NullViolation::create(),
                new RstSample('final class TestAdmin extends AbstractAdmin'),
            ],
        ];
    }
}
