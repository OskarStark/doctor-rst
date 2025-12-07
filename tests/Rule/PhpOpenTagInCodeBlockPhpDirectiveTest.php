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

use App\Rule\PhpOpenTagInCodeBlockPhpDirective;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class PhpOpenTagInCodeBlockPhpDirectiveTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new PhpOpenTagInCodeBlockPhpDirective())->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    public static function checkProvider(): iterable
    {
        foreach (self::phpCodeBlocks() as $codeBlock) {
            yield \sprintf('Has violation for code-block "%s"', $codeBlock) => [
                Violation::from(
                    \sprintf('Please add PHP open tag after "%s" directive', $codeBlock),
                    'filename',
                    1,
                    $codeBlock,
                ),
                new RstSample([
                    $codeBlock,
                    '',
                    '$this->somePhp();',
                ]),
            ];

            yield \sprintf('No violation for code-block "%s"', $codeBlock) => [
                NullViolation::create(),
                new RstSample([
                    $codeBlock,
                    '',
                    '<?php',
                ]),
            ];
        }

        $codeBlock = '.. code-block:: html+php';
        yield \sprintf('No violation for code-block "%s"', $codeBlock) => [
            NullViolation::create(),
            new RstSample([
                $codeBlock,
                '',
                '<html>',
            ]),
        ];
    }
}
