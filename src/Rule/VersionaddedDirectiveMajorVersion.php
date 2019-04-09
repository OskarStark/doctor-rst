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

use App\Handler\RulesHandler;
use App\Rst\RstParser;
use Composer\Semver\VersionParser;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VersionaddedDirectiveMajorVersion extends AbstractRule implements Rule, Configurable
{
    /** @var VersionParser */
    private $versionParser;

    /** @var int */
    private $majorVersion;

    public function __construct(VersionParser $versionParser)
    {
        $this->versionParser = $versionParser;
    }

    public function getConfiguration(): OptionsResolver
    {
        $resolver = new OptionsResolver();
        $resolver
            ->setRequired('major_version')
            ->setAllowedTypes('major_version', 'int')
        ;

        return $resolver;
    }

    public function setOptions(array $options): void
    {
        $resolver = $this->getConfiguration();

        $resolvedOptions = $resolver->resolve($options);

        $this->majorVersion = $resolvedOptions['major_version'];
    }

    public static function getGroups(): array
    {
        return [RulesHandler::GROUP_SYMFONY];
    }

    public function check(\ArrayIterator $lines, int $number)
    {
        $lines->seek($number);
        $line = $lines->current();

        if (!RstParser::directiveIs($line, RstParser::DIRECTIVE_VERSIONADDED)) {
            return;
        }

        if (preg_match(sprintf('/^%s(.*)$/', RstParser::DIRECTIVE_VERSIONADDED), RstParser::clean($lines->current()), $matches)) {
            $version = trim($matches[1]);

            try {
                $normalizedVersion = $this->versionParser->normalize($version);

                list($major, $minor, $patch, $add) = explode('.', $normalizedVersion);

                $major = (int) $major;

                if ($this->majorVersion != $major) {
                    return sprintf(
                        'You are not allowed to use version "%s". Only major version "%s" is allowed.',
                        $version,
                        $this->majorVersion
                    );
                }
            } catch (\UnexpectedValueException $e) {
                return sprintf(
                    'Please provide a numeric version behind "%s" instead of "%s"',
                    RstParser::DIRECTIVE_VERSIONADDED,
                    $version
                );
            }
        }
    }
}
