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

use App\Rule\ExtendController;
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;

class ExtendControllerTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check(?string $expected, RstSample $sample)
    {
        $this->assertSame(
            $expected,
            (new ExtendController())->check($sample->getContent(), $sample->getLineNumber())
        );
    }

    public function checkProvider()
    {
        return [
            [
                'Please extend Controller instead of AbstractController',
                new RstSample('class TestController extends AbstractController'),
            ],

            [
                'Please extend Controller instead of AbstractController',
                new RstSample('    class TestController extends AbstractController'),
            ],
            [
                null,
                new RstSample('class TestController extends Controller'),
            ],
            [
                null,
                new RstSample('    class TestController extends Controller'),
            ],
            [
                'Please use "Symfony\Bundle\FrameworkBundle\Controller\Controller" instead of "Symfony\Bundle\FrameworkBundle\Controller\AbstractController"',
                new RstSample('use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;'),
            ],

            [
                'Please use "Symfony\Bundle\FrameworkBundle\Controller\Controller" instead of "Symfony\Bundle\FrameworkBundle\Controller\AbstractController"',
                new RstSample('    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;'),
            ],
            [
                null,
                new RstSample('use Symfony\Bundle\FrameworkBundle\Controller\Controller;'),
            ],
            [
                null,
                new RstSample('    use Symfony\Bundle\FrameworkBundle\Controller\Controller;'),
            ],
        ];
    }
}
