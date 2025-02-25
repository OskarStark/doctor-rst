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

use App\Rule\NoAdminYaml;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class NoAdminYamlTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new NoAdminYaml())->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    public static function checkProvider(): iterable
    {
        yield from [
            [
                Violation::from(
                    'Please use "services.yaml" instead of "admin.yml"',
                    'filename',
                    1,
                    'register the admin class in admin.yml',
                ),
                new RstSample('register the admin class in admin.yml'),
            ],
            [
                NullViolation::create(),
                new RstSample('register the admin class in services.yaml'),
            ],
            [
                Violation::from(
                    'Please use "services.yaml" instead of "admin.yaml"',
                    'filename',
                    1,
                    'register the admin class in admin.yaml',
                ),
                new RstSample('register the admin class in admin.yaml'),
            ],
            [
                NullViolation::create(),
                new RstSample('register the admin class in services.yaml'),
            ],
            [
                NullViolation::create(),
                new RstSample('# config/packages/sonata_admin.yaml'),
            ],
            [
                NullViolation::create(),
                new RstSample('# config/packages/sonata_doctrine_orm_admin.yaml'),
            ],
            [
                NullViolation::create(),
                new RstSample('# config/packages/sonata_doctrine_mongodb_admin.yaml'),
            ],
        ];
    }
}
