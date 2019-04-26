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
use Symfony\Component\OptionsResolver\OptionsResolver;

class VersionaddedDirectiveMinVersion extends AbstractRule implements Rule, Configurable
{
    /** @var string */
    private $minVersion;

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
        return [Registry::GROUP_SYMFONY];
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

            if (-1 === version_compare($version, $this->minVersion)) {
                return sprintf(
                    'Please only provide "%s" if the version is greater/equal "%s"',
                    RstParser::DIRECTIVE_VERSIONADDED,
                    $this->minVersion
                );
            }
        }
    }
}
