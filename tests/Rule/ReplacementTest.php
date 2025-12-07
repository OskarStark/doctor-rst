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

use App\Rule\Replacement;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class ReplacementTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        $configuredRules = [];

        foreach (Replacement::getList() as $search => $message) {
            $configuredRules[] = (new Replacement())->configure($search, $message);
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
            self::assertEquals($expected, $violations[0]);
        }
    }

    /**
     * @return \Generator<string, array{0: ViolationInterface, 1: RstSample}>
     */
    public static function checkProvider(): iterable
    {
        yield 'empty string' => [NullViolation::create(), new RstSample('')];

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
            // 'eg. in the URL should not be reported as mispelling of `e.g.`
            '.. _`FFmpeg package`: https://ffmpeg.org/',
            'PHPDoc',
            //            '# username is your full Gmail or Google Apps email address', // todo this should be supported by the regex
        ];

        foreach ($valids as $valid) {
            yield $valid => [NullViolation::create(), new RstSample($valid)];

            // add leading spaces
            yield \sprintf('"%s" with leading spaces', $valid) => [NullViolation::create(), new RstSample(\sprintf('    %s', $valid))];
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
            yield $invalid => [
                Violation::from(
                    \sprintf('Please replace "%s" with "%s"', $invalid, $valid),
                    'filename',
                    1,
                    $invalid,
                ),
                new RstSample($invalid),
            ];

            // add leading spaces
            yield \sprintf('"%s" with leading spaces', $invalid) => [
                Violation::from(
                    \sprintf('Please replace "%s" with "%s"', $invalid, $valid),
                    'filename',
                    1,
                    $invalid,
                ),
                new RstSample(\sprintf('    %s', $invalid)),
            ];
        }
    }
}
