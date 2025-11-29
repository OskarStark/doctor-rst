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

use App\Rst\RstParser;
use App\Value\Lines;
use App\Value\NullViolation;
use App\Value\RuleGroup;
use App\Value\Violation;
use App\Value\ViolationInterface;
use Composer\Semver\VersionParser;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class DeprecatedDirectiveMajorVersion extends AbstractRule implements Configurable, LineContentRule
{
    private int $majorVersion;

    public function __construct(
        private readonly VersionParser $versionParser,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): OptionsResolver
    {
        $resolver
            ->setRequired('major_version')
            ->setAllowedTypes('major_version', 'int');

        return $resolver;
    }

    public function setOptions(array $options): void
    {
        $resolver = $this->configureOptions(new OptionsResolver());

        $resolvedOptions = $resolver->resolve($options);

        /** @phpstan-ignore-next-line  */
        $this->majorVersion = $resolvedOptions['major_version'];
    }

    public static function getGroups(): array
    {
        return [RuleGroup::Symfony()];
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

            try {
                $normalizedVersion = $this->versionParser->normalize($version);

                [$major, $minor, $patch, $add] = explode('.', $normalizedVersion);

                $major = (int) $major;

                if ($this->majorVersion !== $major) {
                    $message = \sprintf(
                        'You are not allowed to use version "%s". Only major version "%s" is allowed.',
                        $version,
                        $this->majorVersion,
                    );

                    return Violation::from(
                        $message,
                        $filename,
                        $number + 1,
                        $line,
                    );
                }
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
