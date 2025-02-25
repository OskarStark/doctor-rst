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

use App\Rule\UseNamedConstructorWithoutNewKeywordRule;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class UseNamedConstructorWithoutNewKeywordRuleTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new UseNamedConstructorWithoutNewKeywordRule())->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    public static function checkProvider(): iterable
    {
        $validLines = [
            '$client = new NoPrivateNetworkHttpClient(HttpClient::create());',
            'return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);',
            '$container->register(Ldap::class)->addArgument(new Reference(Adapter::class));',
            'new Status(Status::YES);',
            'return new Response(null, Response::HTTP_TOO_MANY_REQUESTS, $headers);',
        ];

        foreach (self::phpCodeBlocks() as $codeBlock) {
            yield \sprintf('Has violation for code-block "%s"', $codeBlock) => [
                Violation::from(
                    'Please do not use "new" keyword with named constructor',
                    'filename',
                    2,
                    $codeBlock,
                ),
                new RstSample([
                    $codeBlock,
                    '    $uuid = new Uuid::fromString("foobar");',
                ], 1),
            ];

            foreach ($validLines as $line) {
                yield \sprintf('NO violation for line "%s" in code-block "%s"', $line, $codeBlock) => [
                    NullViolation::create(),
                    new RstSample([
                        $codeBlock,
                        '    '.$line,
                    ], 1),
                ];
            }
        }
    }
}
