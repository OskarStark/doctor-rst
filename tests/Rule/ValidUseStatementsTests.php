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

use App\Rule\ValidUseStatements;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class ValidUseStatementsTests extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new ValidUseStatements())->check($sample->lines, $sample->lineNumber, 'filename'),
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
                    'Please do not escape the backslashes in a use statement.',
                    'filename',
                    1,
                    'use Symfony\\\\Component\\\\Form\\\\Extension\\\\Core\\\\Type\\\\FormType;',
                ),
                new RstSample('use Symfony\\\\Component\\\\Form\\\\Extension\\\\Core\\\\Type\\\\FormType;'),
            ],
            [
                NullViolation::create(),
                new RstSample('use Symfony\Component\Form\Extension\Core\Type\FormType;'),
            ],
            [
                NullViolation::create(),
                new RstSample('don\'t use the :class:`Symfony\\\\Bundle\\\\FrameworkBundle\\\\Controller\\\\ControllerTrait`'),
            ],
        ];
    }
}
