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

namespace App\Rule;

use App\Annotations\Rule\Description;
use App\Annotations\Rule\InvalidExample;
use App\Annotations\Rule\ValidExample;
use App\Rst\RstParser;
use App\Value\Lines;
use App\Value\RuleGroup;
use Composer\Semver\VersionParser;

/**
 * @Description("Ensure a versionadded directive has a version which follows SemVer.")
 *
 * @ValidExample(".. versionadded:: 3.4")
 *
 * @InvalidExample({".. versionadded::", ".. versionadded:: foo-bar"})
 */
class VersionaddedDirectiveShouldHaveVersion extends AbstractRule implements LineContentRule
{
    private VersionParser $versionParser;

    public function __construct(VersionParser $versionParser)
    {
        $this->versionParser = $versionParser;
    }

    public static function getGroups(): array
    {
        return [
            RuleGroup::Sonata(),
            RuleGroup::Symfony(),
        ];
    }

    public function check(Lines $lines, int $number): ?string
    {
        $lines->seek($number);
        $line = $lines->current();

        if (!RstParser::directiveIs($line, RstParser::DIRECTIVE_VERSIONADDED)) {
            return null;
        }

        if (preg_match(sprintf('/^%s(.*)$/', RstParser::DIRECTIVE_VERSIONADDED), $lines->current()->clean()->toString(), $matches)) {
            $version = trim($matches[1]);

            if (empty($version)) {
                return sprintf('Please provide a version behind "%s"', RstParser::DIRECTIVE_VERSIONADDED);
            }

            try {
                $this->versionParser->normalize($version);
            } catch (\UnexpectedValueException $e) {
                return sprintf(
                    'Please provide a numeric version behind "%s" instead of "%s"',
                    RstParser::DIRECTIVE_VERSIONADDED,
                    $version
                );
            }
        }

        return null;
    }
}
