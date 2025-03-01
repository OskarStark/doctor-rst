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

namespace Rule;

use App\Rule\ReplaceCodeBlockTypes;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class ReplaceCodeBlockTypesTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        $configuredRules = [];

        foreach (ReplaceCodeBlockTypes::getList() as $search => $message) {
            $configuredRules[] = (new ReplaceCodeBlockTypes())->configure($search, $message);
        }

        $violations = [];

        foreach ($configuredRules as $rule) {
            $violation = $rule->check($sample->lines, $sample->lineNumber, 'filename');

            if (!$violation->isNull()) {
                $violations[] = $violation;
            }
        }

        if ($expected->isNull()) {
            self::assertEmpty($violations);
        } else {
            self::assertCount(1, $violations);
            self::assertEquals($expected, $violations[0]);
        }
    }

    public static function checkProvider(): iterable
    {
        yield 'valid' => [
            NullViolation::create(),
            new RstSample([
                '.. code-block:: twig',
            ]),
        ];

        yield 'invalid jinja' => [
            Violation::from(
                'Please do not use type "jinja" for code-block, use "twig" instead',
                'filename',
                1,
                '.. code-block:: jinja',
            ),
            new RstSample([
                '.. code-block:: jinja',
            ]),
        ];

        yield 'invalid html jinja' => [
            Violation::from(
                'Please do not use type "html+jinja" for code-block, use "html+twig" instead',
                'filename',
                1,
                '.. code-block:: html+jinja',
            ),
            new RstSample([
                '.. code-block:: html+jinja',
            ]),
        ];

        yield 'invalid js' => [
            Violation::from(
                'Please do not use type "js" for code-block, use "javascript" instead',
                'filename',
                1,
                '.. code-block:: js',
            ),
            new RstSample([
                '.. code-block:: js',
            ]),
        ];
    }
}
