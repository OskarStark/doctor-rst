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

use App\Rule\FinalAdminExtensionClasses;
use App\Tests\RstSample;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;

final class FinalAdminExtensionClassesTest extends \App\Tests\UnitTestCase
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
            (new FinalAdminExtensionClasses())->check($sample->lines(), $sample->lineNumber(), 'filename')
        );
    }

    public function checkProvider(): array
    {
        return [
            [
                Violation::from(
                    'Please use "final" for AdminExtension class',
                    'filename',
                    1,
                    ''
                ),
                new RstSample('class TestExtension extends AbstractAdminExtension'),
            ],
            [
                Violation::from(
                    'Please use "final" for AdminExtension class',
                    'filename',
                    1,
                    ''
                ),
                new RstSample('    class TestExtension extends AbstractAdminExtension'),
            ],
            [
                NullViolation::create(),
                new RstSample('final class TestExtension extends AbstractAdminExtension'),
            ],
        ];
    }
}
