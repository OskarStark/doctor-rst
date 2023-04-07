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

final class UseNamedConstructorWithoutNewKeywordRuleTest extends UnitTestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new UseNamedConstructorWithoutNewKeywordRule())->check($sample->lines(), $sample->lineNumber(), 'filename'),
        );
    }

    public static function checkProvider(): \Generator
    {
        foreach (self::phpCodeBlocks() as $codeBlock) {
            yield sprintf('Has violation for code-block "%s"', $codeBlock) => [
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

            yield sprintf('No violation for code-block "%s" - First case', $codeBlock) => [
                NullViolation::create(),
                new RstSample([
                    $codeBlock,
                    '    return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);'
                ], 1),
            ];

            yield sprintf('No violation for code-block "%s" - Second case', $codeBlock) => [
                NullViolation::create(),
                new RstSample([
                    $codeBlock,
                    '    $client = new NoPrivateNetworkHttpClient(HttpClient::create());'
                ], 1),
            ];

            yield sprintf('No violation for code-block "%s" - Third case', $codeBlock) => [
                NullViolation::create(),
                new RstSample([
                    $codeBlock,
                    '    $container->register(Ldap::class)->addArgument(new Reference(Adapter::class));'
                ], 1),
            ];

            yield sprintf('No violation for code-block "%s" - Fourth case', $codeBlock) => [
                NullViolation::create(),
                new RstSample([
                    $codeBlock,
                    '    new Status(Status::YES);',
                ], 1),
            ];

            yield sprintf('No violation for code-block "%s" - Fifth case', $codeBlock) => [
                NullViolation::create(),
                new RstSample([
                    $codeBlock,
                    '    return new Response(null, Response::HTTP_TOO_MANY_REQUESTS, $headers);',
                ], 1),
            ];
        }
    }
}
