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
use App\Handler\Registry;
use App\Rst\RstParser;
use Composer\Semver\VersionParser;

/**
 * @Description("Ensure a deprecated directive has a version which follows SemVer.")
 * @ValidExample(".. deprecated:: 3.4")
 * @InvalidExample({".. deprecated::", ".. deprecated:: foo-bar"})
 */
class DeprecatedDirectiveShouldHaveVersion extends AbstractRule implements Rule
{
    /** @var VersionParser */
    private $versionParser;

    public function __construct(VersionParser $versionParser)
    {
        $this->versionParser = $versionParser;
    }

    public static function getGroups(): array
    {
        return [Registry::GROUP_SONATA, Registry::GROUP_SYMFONY];
    }

    public function check(\ArrayIterator $lines, int $number)
    {
        $lines->seek($number);
        $line = $lines->current();

        if (!RstParser::directiveIs($line, RstParser::DIRECTIVE_DEPRECATED)) {
            return;
        }

        if (preg_match(sprintf('/^%s(.*)$/', RstParser::DIRECTIVE_DEPRECATED), RstParser::clean($lines->current()), $matches)) {
            $version = trim($matches[1]);

            if (empty($version)) {
                return sprintf('Please provide a version behind "%s"', RstParser::DIRECTIVE_DEPRECATED);
            }

            try {
                $this->versionParser->normalize($version);
            } catch (\UnexpectedValueException $e) {
                return sprintf(
                    'Please provide a numeric version behind "%s" instead of "%s"',
                    RstParser::DIRECTIVE_DEPRECATED,
                    $version
                );
            }
        }
    }
}
