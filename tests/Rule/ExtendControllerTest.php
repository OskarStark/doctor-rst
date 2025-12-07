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

use App\Rule\ExtendController;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class ExtendControllerTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new ExtendController())->check($sample->lines, $sample->lineNumber, 'filename'),
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
                    'Please extend Controller instead of AbstractController',
                    'filename',
                    1,
                    'class TestController extends AbstractController',
                ),
                new RstSample('class TestController extends AbstractController'),
            ],
            [
                Violation::from(
                    'Please extend Controller instead of AbstractController',
                    'filename',
                    1,
                    'class TestController extends AbstractController',
                ),
                new RstSample('    class TestController extends AbstractController'),
            ],
            [
                NullViolation::create(),
                new RstSample('class TestController extends Controller'),
            ],
            [
                NullViolation::create(),
                new RstSample('    class TestController extends Controller'),
            ],
            [
                Violation::from(
                    'Please use "Symfony\Bundle\FrameworkBundle\Controller\Controller" instead of "Symfony\Bundle\FrameworkBundle\Controller\AbstractController"',
                    'filename',
                    1,
                    'use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;',
                ),
                new RstSample('use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;'),
            ],
            [
                Violation::from(
                    'Please use "Symfony\Bundle\FrameworkBundle\Controller\Controller" instead of "Symfony\Bundle\FrameworkBundle\Controller\AbstractController"',
                    'filename',
                    1,
                    'use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;',
                ),
                new RstSample('    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;'),
            ],
            [
                NullViolation::create(),
                new RstSample('use Symfony\Bundle\FrameworkBundle\Controller\Controller;'),
            ],
            [
                NullViolation::create(),
                new RstSample('    use Symfony\Bundle\FrameworkBundle\Controller\Controller;'),
            ],
        ];
    }
}
