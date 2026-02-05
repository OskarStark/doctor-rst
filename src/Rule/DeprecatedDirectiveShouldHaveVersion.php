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

namespace App\Rule;

use App\Attribute\Rule\Description;
use App\Attribute\Rule\InvalidExample;
use App\Attribute\Rule\ValidExample;
use App\Rst\RstParser;
use App\Value\Lines;
use App\Value\NullViolation;
use App\Value\RuleGroup;
use App\Value\Violation;
use App\Value\ViolationInterface;
use Composer\Semver\VersionParser;

#[Description('Ensure a deprecated directive has a version which follows SemVer.')]
#[ValidExample('.. deprecated:: 3.4')]
#[InvalidExample('.. deprecated::')]
#[InvalidExample('.. deprecated:: foo-bar')]
final class DeprecatedDirectiveShouldHaveVersion extends AbstractRule implements LineContentRule
{
    public function __construct(
        private readonly VersionParser $versionParser,
    ) {
    }

    public static function getGroups(): array
    {
        return [
            RuleGroup::Sonata(),
            RuleGroup::Symfony(),
        ];
    }

    public function check(Lines $lines, int $number, string $filename): ViolationInterface
    {
        $lines->seek($number);
        $line = $lines->current();

        if (!RstParser::directiveIs($line, RstParser::DIRECTIVE_DEPRECATED)) {
            return NullViolation::create();
        }

        if ($matches = $line->clean()->match(\sprintf('/^%s(.*)$/', RstParser::DIRECTIVE_DEPRECATED))) {
            /** @var string[] $matches */
            $version = trim((string) $matches[1]);

            if (empty($version)) {
                return Violation::from(
                    \sprintf('Please provide a version behind "%s"', RstParser::DIRECTIVE_DEPRECATED),
                    $filename,
                    $number + 1,
                    $line,
                );
            }

            try {
                $this->versionParser->normalize($version);
            } catch (\UnexpectedValueException) {
                $message = \sprintf(
                    'Please provide a numeric version behind "%s" instead of "%s"',
                    RstParser::DIRECTIVE_DEPRECATED,
                    $version,
                );

                return Violation::from(
                    $message,
                    $filename,
                    $number + 1,
                    $line,
                );
            }
        }

        return NullViolation::create();
    }
}
