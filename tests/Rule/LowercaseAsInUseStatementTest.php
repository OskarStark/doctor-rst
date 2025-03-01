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

use App\Rule\LowercaseAsInUseStatements;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class LowercaseAsInUseStatementTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new LowercaseAsInUseStatements())->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    public static function checkProvider(): iterable
    {
        $codeBlocks = self::phpCodeBlocks();

        // VALID
        foreach ($codeBlocks as $codeBlock) {
            // WITH blank line after directive
            yield [
                NullViolation::create(),
                new RstSample([
                    $codeBlock,
                    '',
                    '    use Symfony\A as A;',
                ]),
            ];

            // WITHOUT blank line after directive
            yield [
                NullViolation::create(),
                new RstSample([
                    $codeBlock,
                    '    use Symfony\A as A;',
                ]),
            ];
        }

        // INVALID
        foreach ($codeBlocks as $codeBlock) {
            foreach (['AS', 'As', 'aS'] as $invalid) {
                // WITH blank line after directive
                yield [
                    Violation::from(
                        \sprintf('Please use lowercase "as" instead of "%s"', $invalid),
                        'filename',
                        3,
                        \sprintf('use Symfony\A %s A;', $invalid),
                    ),
                    new RstSample([
                        $codeBlock,
                        '',
                        \sprintf('    use Symfony\A %s A;', $invalid),
                    ], 2),
                ];

                // WITHOUT blank line after directive
                yield [
                    Violation::from(
                        \sprintf('Please use lowercase "as" instead of "%s"', $invalid),
                        'filename',
                        2,
                        \sprintf('use Symfony\A %s A;', $invalid),
                    ),
                    new RstSample([
                        $codeBlock,
                        \sprintf('    use Symfony\A %s A;', $invalid),
                    ], 1),
                ];
            }
        }
    }
}
