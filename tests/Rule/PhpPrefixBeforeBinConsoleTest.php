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

use App\Rule\PhpPrefixBeforeBinConsole;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class PhpPrefixBeforeBinConsoleTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new PhpPrefixBeforeBinConsole())->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    public static function checkProvider(): iterable
    {
        yield [NullViolation::create(), new RstSample('please execute php bin/console foo')];
        yield [NullViolation::create(), new RstSample('you can use `bin/console` to execute')];
        yield [NullViolation::create(), new RstSample('php "%s/../bin/console"')];
        yield [NullViolation::create(), new RstSample('.. _`copying Symfony\'s bin/console source`: https://github.com/symfony/recipes/blob/master/symfony/console/3.3/bin/console')];
        yield [NullViolation::create(), new RstSample('├─ bin/console')];
        yield [NullViolation::create(), new RstSample('Symfony\Component\Console\Application->run() at /home/greg/demo/bin/console:42')];
        yield [NullViolation::create(), new RstSample('// bin/console')];
        yield [NullViolation::create(), new RstSample('$childProcess = new PhpSubprocess([\'bin/console\', \'cache:pool:prune\']);')];
        yield [
            Violation::from(
                'Please add "php" prefix before "bin/console"',
                'filename',
                1,
                'please execute bin/console foo',
            ),
            new RstSample('please execute bin/console foo'),
        ];
    }
}
