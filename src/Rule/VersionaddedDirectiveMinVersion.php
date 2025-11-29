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
use Symfony\Component\OptionsResolver\OptionsResolver;

final class VersionaddedDirectiveMinVersion extends AbstractRule implements Configurable, LineContentRule
{
    private string $minVersion;

    public function configureOptions(OptionsResolver $resolver): OptionsResolver
    {
        $resolver
            ->setRequired('min_version')
            ->setAllowedTypes('min_version', 'string');

        return $resolver;
    }

    public function setOptions(array $options): void
    {
        $resolver = $this->configureOptions(new OptionsResolver());

        $resolvedOptions = $resolver->resolve($options);

        /** @phpstan-ignore-next-line  */
        $this->minVersion = $resolvedOptions['min_version'];
    }

    public static function getGroups(): array
    {
        return [RuleGroup::Symfony()];
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

            if (-1 === version_compare($version, $this->minVersion)) {
                $message = \sprintf(
                    'Please only provide "%s" if the version is greater/equal "%s"',
                    RstParser::DIRECTIVE_VERSIONADDED,
                    $this->minVersion,
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
