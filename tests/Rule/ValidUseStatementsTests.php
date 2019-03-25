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

use App\Rule\ValidUseStatements;
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;

class ValidUseStatementsTests extends TestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check($expected, RstSample $sample)
    {
        $this->assertSame(
            $expected,
            (new ValidUseStatements())->check($sample->getContent(), $sample->getLineNumber())
        );
    }

    public function checkProvider()
    {
        return [
            [
                'Please do not escape the backslashes in a use statement.',
                new RstSample('use Symfony\\\\Component\\\\Form\\\\Extension\\\\Core\\\\Type\\\\FormType;'),
            ],
            [
                null,
                new RstSample('use Symfony\Component\Form\Extension\Core\Type\FormType;'),
            ],
            [
                null,
                new RstSample('don\'t use the :class:`Symfony\\\\Bundle\\\\FrameworkBundle\\\\Controller\\\\ControllerTrait`'),
            ],
        ];
    }
}
