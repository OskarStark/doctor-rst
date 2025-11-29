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

#[Description('Ensure a versionadded directive has a version which follows SemVer.')]
#[ValidExample('.. versionadded:: 3.4')]
#[InvalidExample('.. versionadded::')]
#[InvalidExample('.. versionadded:: foo-bar')]
final class VersionaddedDirectiveShouldHaveVersion extends AbstractRule implements LineContentRule
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

        if (!RstParser::directiveIs($line, RstParser::DIRECTIVE_VERSIONADDED)) {
            return NullViolation::create();
        }

        if (preg_match(\sprintf('/^%s(.*)$/', RstParser::DIRECTIVE_VERSIONADDED), $line->clean()->toString(), $matches)) {
            $version = trim($matches[1]);

            if (empty($version)) {
                return Violation::from(
                    \sprintf('Please provide a version behind "%s"', RstParser::DIRECTIVE_VERSIONADDED),
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
                    RstParser::DIRECTIVE_VERSIONADDED,
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
