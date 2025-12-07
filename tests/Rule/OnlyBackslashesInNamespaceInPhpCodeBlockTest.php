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

use App\Rule\OnlyBackslashesInNamespaceInPhpCodeBlock;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class OnlyBackslashesInNamespaceInPhpCodeBlockTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new OnlyBackslashesInNamespaceInPhpCodeBlock())->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    public static function checkProvider(): iterable
    {
        foreach (self::phpCodeBlocks() as $codeBlock) {
            yield [
                Violation::from(
                    'Please check "namespace App/Handler;", it should not contain "/"',
                    'filename',
                    5,
                    'namespace App/Handler;',
                ),
                new RstSample([
                    $codeBlock,
                    '',
                    '    // src/Handler/Collection.php',
                    '',
                    '    namespace App/Handler;',
                ], 4),
            ];

            yield [
                Violation::from(
                    'Please check "NaMeSpaCe App/Handler;", it should not contain "/"',
                    'filename',
                    5,
                    'NaMeSpaCe App/Handler;',
                ),
                new RstSample([
                    $codeBlock,
                    '',
                    '    // src/Handler/Collection.php',
                    '',
                    '    NaMeSpaCe App/Handler;',
                ], 4),
            ];
        }

        yield [
            Violation::from(
                'Please check "namespace App/Handler;", it should not contain "/"',
                'filename',
                5,
                'namespace App/Handler;',
            ),
            new RstSample([
                '::',
                '',
                '    // src/Handler/Collection.php',
                '',
                '    namespace App/Handler;',
            ], 4),
        ];

        yield [
            NullViolation::create(),
            new RstSample('temp'),
        ];
    }
}
