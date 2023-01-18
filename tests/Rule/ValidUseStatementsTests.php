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

final class ValidUseStatementsTests extends \App\Tests\UnitTestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check(?string $expected, RstSample $sample): void
    {
        static::assertSame(
            $expected,
            (new ValidUseStatements())->check($sample->lines(), $sample->lineNumber())
        );
    }

    /**
     * @return array<array{0: string|null, 1: RstSample}>
     */
    public function checkProvider(): array
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
