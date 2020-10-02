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

use App\Rule\Replacement;
use App\Rule\Rule;
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;

class ReplacementTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check(?string $expected, RstSample $sample)
    {
        $configuredRules = [];
        foreach (Replacement::getList() as $search => $message) {
            $configuredRules[] = (new Replacement())->configure($search, $message);
        }

        $violations = [];
        /** @var Rule $rule */
        foreach ($configuredRules as $rule) {
            $violation = $rule->check($sample->lines(), $sample->lineNumber());
            if (null !== $violation) {
                $violations[] = $violation;
            }
        }

        if (null === $expected) {
            static::assertCount(0, $violations);
        } else {
            static::assertCount(1, $violations);
            static::assertSame($expected, $violations[0]);
        }
    }

    public function checkProvider()
    {
        yield 'empty string' => [null, new RstSample('')];

        $valids = [
            'http://...',
            'transport://..',
            '// ...',
            '# ...',
            '<!-- ... -->',
            'Applications',
            'applications',
            'Type-hint',
            'type-hint',
            '<?xml version="1.0" encoding="UTF-8" ?>',
            '$filesystem',
            'Content-Type',
            '--env=prod',
            '--env=test',
            'End-to-End',
            'information',
            'Information',
            'performance',
            'Performance',
            '``%kernel.debug%``',
            'e.g.',
            'PHPDoc',
//            '# username is your full Gmail or Google Apps email address', // todo this should be supported by the regex
        ];

        foreach ($valids as $valid) {
            yield $valid => [null, new RstSample($valid)];

            // add leading spaces
            yield sprintf('"%s" with leading spaces', $valid) => [null, new RstSample(sprintf('    %s', $valid))];
        }

        $invalids = [
            '// ..' => '// ...',
            '//..' => '// ...',
            '# ..' => '# ...',
            '#..' => '# ...',
            '<!-- .. -->' => '<!-- ... -->',
            '<!--..-->' => '<!-- ... -->',
            '{# .. #}' => '{# ... #}',
            '{#..#}' => '{# ... #}',
            'Apps' => 'Applications',
            'apps' => 'applications',
            'Typehint' => 'Type-hint',
            'typehint' => 'type-hint',
            'encoding="utf-8"' => 'encoding="UTF-8"',
            '$fileSystem' => '$filesystem',
            'Content-type' => 'Content-Type',
            '--env prod' => '--env=prod',
            '--env test' => '--env=test',
            'End 2 End' => 'End-to-End',
            'E2E' => 'End-to-End',
            'informations' => 'information',
            'Informations' => 'Information',
            'performances' => 'performance',
            'Performances' => 'Performance',
            '``\'%kernel.debug%\'``' => '``%kernel.debug%``',
            'PHPdoc' => 'PHPDoc',
            'eg.' => 'e.g.',
        ];

        foreach ($invalids as $invalid => $valid) {
            yield $invalid => [sprintf('Please replace "%s" with "%s"', $invalid, $valid), new RstSample($invalid)];

            // add leading spaces
            yield sprintf('"%s" with leading spaces', $invalid) => [sprintf('Please replace "%s" with "%s"', $invalid, $valid), new RstSample(sprintf('    %s', $invalid))];
        }
    }
}
