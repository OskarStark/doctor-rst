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

use App\Rst\RstParser;
use App\Value\Lines;
use App\Value\RuleGroup;
use Composer\Semver\VersionParser;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeprecatedDirectiveMajorVersion extends AbstractRule implements Rule, Configurable
{
    private VersionParser $versionParser;
    private int $majorVersion;

    public function __construct(VersionParser $versionParser)
    {
        $this->versionParser = $versionParser;
    }

    public function configureOptions(OptionsResolver $resolver): OptionsResolver
    {
        $resolver
            ->setRequired('major_version')
            ->setAllowedTypes('major_version', 'int')
        ;

        return $resolver;
    }

    public function setOptions(array $options): void
    {
        $resolver = $this->configureOptions(new OptionsResolver());

        $resolvedOptions = $resolver->resolve($options);

        $this->majorVersion = $resolvedOptions['major_version'];
    }

    public static function getGroups(): array
    {
        return [RuleGroup::Symfony()];
    }

    public function check(Lines $lines, int $number): ?string
    {
        $lines->seek($number);
        $line = $lines->current();

        if (!RstParser::directiveIs($line, RstParser::DIRECTIVE_DEPRECATED)) {
            return null;
        }

        if (preg_match(sprintf('/^%s(.*)$/', RstParser::DIRECTIVE_DEPRECATED), $lines->current()->clean(), $matches)) {
            $version = trim($matches[1]);

            try {
                $normalizedVersion = $this->versionParser->normalize($version);

                list($major, $minor, $patch, $add) = explode('.', $normalizedVersion);

                $major = (int) $major;

                if ($this->majorVersion !== $major) {
                    return sprintf(
                        'You are not allowed to use version "%s". Only major version "%s" is allowed.',
                        $version,
                        $this->majorVersion
                    );
                }
            } catch (\UnexpectedValueException $e) {
                return sprintf(
                    'Please provide a numeric version behind "%s" instead of "%s"',
                    RstParser::DIRECTIVE_DEPRECATED,
                    $version
                );
            }
        }

        return null;
    }
}
