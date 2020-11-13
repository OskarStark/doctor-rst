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

use App\Value\Lines;
use App\Value\RuleGroup;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MaxBlankLines extends AbstractRule implements Rule, Configurable
{
    private int $max;

    public function configureOptions(OptionsResolver $resolver): OptionsResolver
    {
        $resolver
            ->setDefault('max', 2)
            ->setRequired('max')
            ->setAllowedTypes('max', 'int')
        ;

        return $resolver;
    }

    public function setOptions(array $options): void
    {
        $resolver = $this->configureOptions(new OptionsResolver());

        $resolvedOptions = $resolver->resolve($options);

        $this->max = $resolvedOptions['max'];
    }

    public static function runOnlyOnBlankline(): bool
    {
        return true;
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

        if (!$lines->current()->isBlank()) {
            return null;
        }

        $blanklines = 1;
        $lines->next();

        while ($lines->valid() && $lines->current()->isBlank()) {
            $lines->current()->markProcessedBy(self::class);
            ++$blanklines;

            $lines->next();
        }

        if ($blanklines > $this->max) {
            return sprintf('Please use max %s blank lines, you used %s', $this->max, $blanklines);
        }

        return null;
    }
}
