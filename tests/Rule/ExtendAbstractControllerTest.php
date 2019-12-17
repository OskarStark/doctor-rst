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

use App\Rule\ExtendAbstractController;
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;

class ExtendAbstractControllerTest extends TestCase
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
            (new ExtendAbstractController())->check($sample->lines(), $sample->lineNumber())
        );
    }

    public function checkProvider()
    {
        return [
            [
                'Please extend AbstractController instead of Controller',
                new RstSample('class TestController extends Controller'),
            ],

            [
                'Please extend AbstractController instead of Controller',
                new RstSample('    class TestController extends Controller'),
            ],
            [
                null,
                new RstSample('class TestController extends AbstractController'),
            ],
            [
                null,
                new RstSample('    class TestController extends AbstractController'),
            ],
            [
                'Please use "Symfony\Bundle\FrameworkBundle\Controller\AbstractController" instead of "Symfony\Bundle\FrameworkBundle\Controller\Controller"',
                new RstSample('use Symfony\Bundle\FrameworkBundle\Controller\Controller;'),
            ],

            [
                'Please use "Symfony\Bundle\FrameworkBundle\Controller\AbstractController" instead of "Symfony\Bundle\FrameworkBundle\Controller\Controller"',
                new RstSample('    use Symfony\Bundle\FrameworkBundle\Controller\Controller;'),
            ],
            [
                null,
                new RstSample('use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;'),
            ],
            [
                null,
                new RstSample('    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;'),
            ],
        ];
    }
}
