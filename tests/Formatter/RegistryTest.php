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

namespace App\Tests\Formatter;

use App\Formatter\GithubFormatter;
use App\Formatter\Registry;
use PHPUnit\Framework\TestCase;

class RegistryTest extends TestCase
{
    public function testInvalidNameThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Formatter "invalid" not found');

        (new Registry(new GithubFormatter()))->get('invalid');
    }

    public function testValidName(): void
    {
        $formatter = new GithubFormatter();

        static::assertSame($formatter, (new Registry($formatter))->get('github'));
    }
}
