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

use App\Rule\FinalAdminClasses;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class FinalAdminClassesTest extends UnitTestCase
{
    #[Test]
    public function getName(): void
    {
        self::assertSame('final_admin_classes', FinalAdminClasses::getName()->toString());
    }

    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new FinalAdminClasses())->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    /**
     * @return array<array{0: ViolationInterface, 1: RstSample}>
     */
    public static function checkProvider(): iterable
    {
        yield from [
            [
                Violation::from(
                    'Please use "final" for Admin class',
                    'filename',
                    1,
                    'class TestAdmin extends AbstractAdmin',
                ),
                new RstSample('class TestAdmin extends AbstractAdmin'),
            ],
            [
                Violation::from(
                    'Please use "final" for Admin class',
                    'filename',
                    1,
                    'class TestAdmin extends AbstractAdmin',
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
