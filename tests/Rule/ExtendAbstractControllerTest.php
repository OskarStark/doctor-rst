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

use App\Rule\ExtendAbstractController;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class ExtendAbstractControllerTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new ExtendAbstractController())->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    public static function checkProvider(): iterable
    {
        yield from [
            [
                Violation::from(
                    'Please extend AbstractController instead of Controller',
                    'filename',
                    1,
                    'class TestController extends Controller',
                ),
                new RstSample('class TestController extends Controller'),
            ],
            [
                Violation::from(
                    'Please extend AbstractController instead of Controller',
                    'filename',
                    1,
                    'class TestController extends Controller',
                ),
                new RstSample('    class TestController extends Controller'),
            ],
            [
                NullViolation::create(),
                new RstSample('class TestController extends AbstractController'),
            ],
            [
                NullViolation::create(),
                new RstSample('    class TestController extends AbstractController'),
            ],
            [
                Violation::from(
                    'Please use "Symfony\Bundle\FrameworkBundle\Controller\AbstractController" instead of "Symfony\Bundle\FrameworkBundle\Controller\Controller"',
                    'filename',
                    1,
                    'use Symfony\Bundle\FrameworkBundle\Controller\Controller;',
                ),
                new RstSample('use Symfony\Bundle\FrameworkBundle\Controller\Controller;'),
            ],
            [
                Violation::from(
                    'Please use "Symfony\Bundle\FrameworkBundle\Controller\AbstractController" instead of "Symfony\Bundle\FrameworkBundle\Controller\Controller"',
                    'filename',
                    1,
                    'use Symfony\Bundle\FrameworkBundle\Controller\Controller;',
                ),
                new RstSample('    use Symfony\Bundle\FrameworkBundle\Controller\Controller;'),
            ],
            [
                NullViolation::create(),
                new RstSample('use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;'),
            ],
            [
                NullViolation::create(),
                new RstSample('    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;'),
            ],
        ];
    }
}
