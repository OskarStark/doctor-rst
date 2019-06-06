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

use App\Rule\Rule;
use App\Rule\Typo;
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;

class TypoTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check($expected, RstSample $sample)
    {
        $configuredRules = [];
        foreach (Typo::getList() as $search => $message) {
            $configuredRules[] = (new Typo())->configure($search, $message);
        }

        $violations = [];
        /** @var Rule $rule */
        foreach ($configuredRules as $rule) {
            $violation = $rule->check($sample->getContent(), $sample->getLineNumber());
            if (null !== $violation) {
                $violations[] = $violation;
            }
        }

        if (null === $expected) {
            $this->assertCount(0, $violations);
        } else {
            $this->assertCount(1, $violations);
            $this->assertStringStartsWith($expected, $violations[0]);
        }
    }

    public function checkProvider()
    {
        yield 'empty string' => [null, new RstSample('')];

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
        ];

        foreach ($valids as $valid) {
            yield $valid => [null, new RstSample($valid)];

            // add leading spaces
            yield sprintf('"%s" with leading spaces', $valid) => [null, new RstSample(sprintf('    %s', $valid))];
        }

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
        ];

        foreach ($invalids as $invalid) {
            yield $invalid => [sprintf('Typo in word "%s"', $invalid), new RstSample($invalid)];

            // add leading spaces
            yield sprintf('"%s" with leading spaces', $invalid) => [sprintf('Typo in word "%s"', $invalid), new RstSample(sprintf('    %s', $invalid))];
        }
    }
}
