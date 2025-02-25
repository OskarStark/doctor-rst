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

use App\Rule\BlankLineAfterFilepathInXmlCodeBlock;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class BlankLineAfterFilepathInXmlCodeBlockTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new BlankLineAfterFilepathInXmlCodeBlock())->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    /**
     * @return \Generator<array{0: ViolationInterface, 1: RstSample}>
     */
    public static function checkProvider(): iterable
    {
        $paths = [
            'config/services.xml',
            'translations/messages.xlf',
            'translations/messages.xliff',
        ];

        foreach ($paths as $path) {
            yield [
                Violation::from(
                    \sprintf('Please add a blank line after "<!-- %s -->"', $path),
                    'filename',
                    3,
                    \sprintf('<!-- %s -->', $path),
                ),
                new RstSample([
                    '.. code-block:: xml',
                    '',
                    \sprintf('<!-- %s -->', $path),
                    '    <foo\/>',
                ]),
            ];

            yield [
                NullViolation::create(),
                new RstSample([
                    '.. code-block:: xml',
                    '',
                    \sprintf('    <!-- %s -->', $path),
                    '',
                    '    <foo\/>',
                ]),
            ];

            yield [
                Violation::from(
                    \sprintf('Please add a blank line after "<!--%s-->"', $path),
                    'filename',
                    3,
                    \sprintf('<!--%s-->', $path),
                ),
                new RstSample([
                    '.. code-block:: xml',
                    '',
                    \sprintf('    <!--%s-->', $path),
                    '    <foo\/>',
                ]),
            ];

            yield [
                NullViolation::create(),
                new RstSample([
                    '.. code-block:: xml',
                    '',
                    \sprintf('    <!--%s-->', $path),
                    '',
                    '    <foo\/>',
                ]),
            ];

            yield [
                NullViolation::create(),
                new RstSample([
                    '.. code-block:: xml',
                    '',
                    \sprintf('    <!--%s-->', $path),
                    '    <!-- a comment -->',
                    '    <foo\/>',
                ]),
            ];
        }

        yield [
            NullViolation::create(),
            new RstSample('temp'),
        ];
    }
}
