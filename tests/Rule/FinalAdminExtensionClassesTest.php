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

use App\Rule\FinalAdminExtensionClasses;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class FinalAdminExtensionClassesTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new FinalAdminExtensionClasses())->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    public static function checkProvider(): iterable
    {
        yield from [
            [
                Violation::from(
                    'Please use "final" for AdminExtension class',
                    'filename',
                    1,
                    'class TestExtension extends AbstractAdminExtension',
                ),
                new RstSample('class TestExtension extends AbstractAdminExtension'),
            ],
            [
                Violation::from(
                    'Please use "final" for AdminExtension class',
                    'filename',
                    1,
                    'class TestExtension extends AbstractAdminExtension',
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
