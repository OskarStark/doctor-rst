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

use App\Handler\Registry;
use App\Rst\RstParser;
use App\Value\Lines;
use App\Value\RuleGroup;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeprecatedDirectiveMinVersion extends AbstractRule implements Rule, Configurable
{
    private string $minVersion;

    public function configureOptions(OptionsResolver $resolver): OptionsResolver
    {
        $resolver
            ->setRequired('min_version')
            ->setAllowedTypes('min_version', 'string')
        ;

        return $resolver;
    }

    public function setOptions(array $options): void
    {
        $resolver = $this->configureOptions(new OptionsResolver());

        $resolvedOptions = $resolver->resolve($options);

        $this->minVersion = $resolvedOptions['min_version'];
    }

    public static function getGroups(): array
    {
        return [RuleGroup::fromString(Registry::GROUP_SYMFONY)];
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

            if (-1 === version_compare($version, $this->minVersion)) {
                return sprintf(
                    'Please only provide "%s" if the version is greater/equal "%s"',
                    RstParser::DIRECTIVE_DEPRECATED,
                    $this->minVersion
                );
            }
        }

        return null;
    }
}
