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

namespace App\Tests\Formatter;

use App\Formatter\ConsoleFormatter;
use App\Formatter\Exception\FormatterNotFound;
use App\Formatter\Registry;
use App\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\Test;

final class RegistryTest extends UnitTestCase
{
    #[Test]
    public function invalidNameThrowsException(): void
    {
        $this->expectException(FormatterNotFound::class);
        $this->expectExceptionMessage('Formatter "invalid" not found');

        (new Registry(new ConsoleFormatter()))->get('invalid');
    }

    #[Test]
    public function validName(): void
    {
        $formatter = new ConsoleFormatter();

        self::assertSame($formatter, (new Registry($formatter))->get('console'));
    }
}
