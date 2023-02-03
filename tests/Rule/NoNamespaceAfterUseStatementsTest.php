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

use App\Rule\NoNamespaceAfterUseStatements;
use App\Tests\RstSample;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;

final class NoNamespaceAfterUseStatementsTest extends \App\Tests\UnitTestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        static::assertEquals(
            $expected,
            (new NoNamespaceAfterUseStatements())->check($sample->lines(), $sample->lineNumber(), 'filename')
        );
    }

    public function checkProvider(): \Generator
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
                    '    namespace App;',
                    '    use Symfony\A;',
                ]),
            ];

            // WITHOUT blank line after directive
            yield [
                NullViolation::create(),
                new RstSample([
                    $codeBlock,
                    '    namespace App;',
                    '    use Symfony\A;',
                ]),
            ];
        }

        // INVALID
        foreach ($codeBlocks as $codeBlock) {
            // WITH blank line after directive
            yield [
                Violation::from(
                    'Please move the namespace before the use statement(s)',
                    'filename',
                    1,
                    'namespace App;',
                ),
                new RstSample([
                    $codeBlock,
                    '',
                    '    use Symfony\A;',
                    '    namespace App;',
                ]),
            ];

            // WITHOUT blank line after directive
            yield [
                Violation::from(
                    'Please move the namespace before the use statement(s)',
                    'filename',
                    1,
                    'namespace App;',
                ),
                new RstSample([
                    $codeBlock,
                    '    use Symfony\A;',
                    '    namespace App;',
                ]),
            ];
        }
    }
}
