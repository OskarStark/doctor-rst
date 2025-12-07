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

use App\Rule\Typo;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class TypoTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('invalidProvider')]
    #[DataProvider('validProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        $configuredRules = [];

        foreach (Typo::getList() as $search => $message) {
            $configuredRules[] = (new Typo())->configure($search, $message);
        }

        $violations = [];

        foreach ($configuredRules as $rule) {
            $violation = $rule->check($sample->lines, $sample->lineNumber, 'filename');

            if (!$violation->isNull()) {
                $violations[] = $violation;
            }
        }

        if ($expected->isNull()) {
            self::assertEmpty($violations);
        } else {
            self::assertCount(1, $violations);
            $expectedMessage = $expected->message();
            \assert('' !== $expectedMessage);
            self::assertStringStartsWith($expectedMessage, $violations[0]->message());
        }
    }

    /**
     * @return \Generator<string, array{0: ViolationInterface, 1: RstSample}>
     */
    public static function validProvider(): iterable
    {
        yield 'empty string' => [NullViolation::create(), new RstSample('')];

        $valids = [
            'Composer',
            'composer',
            'registerBundles()',
            'return',
            'Displays',
            'displays',
            'Maintains',
            'maintains',
            'Doctrine',
            'doctrine',
            'Address',
            'address',
            'argon2i',
            'Description',
            'description',
            'Recalculate',
            'recalculate',
            'achieved',
            'overridden',
            'Successfully',
            'successfully',
            'Optionally',
            'optionally',
            'Estimated',
            'estimated',
            'Strength',
            'strength',
            'Method',
            'method',
            'Constraint',
            'constraint',
            'Instantiation',
            'instantiation',
        ];

        foreach ($valids as $valid) {
            yield $valid => [NullViolation::create(), new RstSample($valid)];

            // add leading spaces
            yield \sprintf('"%s" with leading spaces', $valid) => [NullViolation::create(), new RstSample(\sprintf('    %s', $valid))];
        }
    }

    /**
     * @return \Generator<string, array{0: ViolationInterface, 1: RstSample}>
     */
    public static function invalidProvider(): iterable
    {
        $invalids = [
            'Compsoer',
            'compsoer',
            'registerbundles()',
            'retun',
            'Displayes',
            'displayes',
            'Mantains',
            'mantains',
            'Doctine',
            'doctine',
            'Adress',
            'adress',
            'argon21',
            'Descritpion',
            'descritpion',
            'Recalcuate',
            'recalcuate',
            'achived',
            'overriden',
            'Succesfully',
            'succesfully',
            'Optionnally',
            'optionnally',
            'Esimated',
            'esimated',
            'Strengh',
            'strengh',
            'Mehtod',
            'mehtod',
            'Contraint',
            'contraint',
            'Instanciation',
            'instanciation',
        ];

        foreach ($invalids as $invalid) {
            yield $invalid => [
                Violation::from(
                    \sprintf('Typo in word "%s"', $invalid),
                    'filename',
                    1,
                    $invalid,
                ),
                new RstSample($invalid),
            ];

            // add leading spaces
            yield \sprintf('"%s" with leading spaces', $invalid) => [
                Violation::from(
                    \sprintf('Typo in word "%s"', $invalid),
                    'filename',
                    1,
                    trim($invalid),
                ),
                new RstSample(\sprintf('    %s', $invalid)),
            ];
        }
    }
}
