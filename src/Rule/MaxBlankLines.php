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
use App\Value\NullViolation;
use App\Value\RuleGroup;
use App\Value\Violation;
use App\Value\ViolationInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MaxBlankLines extends AbstractRule implements LineContentRule, Configurable
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

    public function check(Lines $lines, int $number, string $filename): ViolationInterface
    {
        $lines->seek($number);
        $line = $lines->current();

        if (!$line->isBlank()) {
            return NullViolation::create();
        }

        $blanklines = 1;
        $lines->next();

        while ($lines->valid() && $lines->current()->isBlank()) {
            $lines->current()->markProcessedBy(self::class);
            ++$blanklines;

            $lines->next();
        }

        if ($blanklines > $this->max) {
            return Violation::from(
                sprintf('Please use max %s blank lines, you used %s', $this->max, $blanklines),
                $filename,
                $number + 1,
                $line
            );
        }

        return NullViolation::create();
    }
}
