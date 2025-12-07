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

use App\Rule\EnsureGithubDirectiveStartWithPrefix;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class EnsureGithubDirectiveStartWithPrefixTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, string $prefix, RstSample $sample): void
    {
        $rule = (new EnsureGithubDirectiveStartWithPrefix());
        $rule->setOptions(['prefix' => $prefix]);

        self::assertEquals($expected, $rule->check($sample->lines, $sample->lineNumber, 'filename'));
    }

    /**
     * @return \Generator<array{0: ViolationInterface, 1: string, 2: RstSample}>
     */
    public static function checkProvider(): iterable
    {
        yield [
            NullViolation::create(),
            'Symfony',
            new RstSample('Using :class:`Symfony\\Component\\Cache\\Adapter\\PdoAdapter` is deprecated'),
        ];
        yield [
            NullViolation::create(),
            'Psr',
            new RstSample('Implements the interface :class:`Psr\\Cache\\CacheItemPoolInterface`'),
        ];
        yield [
            NullViolation::create(),
            'Symfony',
            new RstSample('Or :method:`Form::submit() <Symfony\\Component\\Form\\Form::submit>`.'),
        ];
        yield [
            Violation::from(
                'Please only use "Symfony" base namespace with Github directive',
                'filename',
                1,
                'Implements the interface :class:`Psr\\Cache\\CacheItemPoolInterface`',
            ),
            'Symfony',
            new RstSample('Implements the interface :class:`Psr\\Cache\\CacheItemPoolInterface`'),
        ];
        yield [
            Violation::from(
                'Please only use "Psr" base namespace with Github directive',
                'filename',
                1,
                'Or :method:`Form::submit() <Symfony\\Component\\Form\\Form::submit>`.',
            ),
            'Psr',
            new RstSample('Or :method:`Form::submit() <Symfony\\Component\\Form\\Form::submit>`.'),
        ];
    }
}
