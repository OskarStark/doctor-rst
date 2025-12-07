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

use App\Rule\ExtendAbstractAdmin;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class ExtendAbstractAdminTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new ExtendAbstractAdmin())->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    public static function checkProvider(): iterable
    {
        yield from [
            [
                Violation::from(
                    'Please extend AbstractAdmin instead of Admin',
                    'filename',
                    1,
                    'class TestAdmin extends Admin',
                ),
                new RstSample('class TestAdmin extends Admin'),
            ],
            [
                Violation::from(
                    'Please extend AbstractAdmin instead of Admin',
                    'filename',
                    1,
                    'class TestAdmin extends Admin',
                ),
                new RstSample('    class TestAdmin extends Admin'),
            ],
            [
                NullViolation::create(),
                new RstSample('class TestAdmin extends AbstractAdmin'),
            ],
            [
                NullViolation::create(),
                new RstSample('    class TestAdmin extends AbstractAdmin'),
            ],
            [
                Violation::from(
                    'Please use "Sonata\AdminBundle\Admin\AbstractAdmin" instead of "Sonata\AdminBundle\Admin\Admin"',
                    'filename',
                    1,
                    'use Sonata\AdminBundle\Admin\Admin;',
                ),
                new RstSample('use Sonata\AdminBundle\Admin\Admin;'),
            ],
            [
                Violation::from(
                    'Please use "Sonata\AdminBundle\Admin\AbstractAdmin" instead of "Sonata\AdminBundle\Admin\Admin"',
                    'filename',
                    1,
                    'use Sonata\AdminBundle\Admin\Admin;',
                ),
                new RstSample('    use Sonata\AdminBundle\Admin\Admin;'),
            ],
            [
                NullViolation::create(),
                new RstSample('use Sonata\AdminBundle\Admin\AbstractAdmin;'),
            ],
            [
                NullViolation::create(),
                new RstSample('    use Sonata\AdminBundle\Admin\AbstractAdmin;'),
            ],
        ];
    }
}
