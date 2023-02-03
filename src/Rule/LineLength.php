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
use App\Value\Violation;
use App\Value\ViolationInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LineLength extends AbstractRule implements LineContentRule, Configurable
{
    private int $max;

    public function configureOptions(OptionsResolver $resolver): OptionsResolver
    {
        $resolver
            ->setDefault('max', 80)
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

    public function check(Lines $lines, int $number, string $filename): ViolationInterface
    {
        $lines->seek($number);
        $line = $lines->current();

        $count = mb_strlen($line->clean()->toString());

        if ($count > $this->max) {
            return Violation::from(
                sprintf('Line is to long (max %s) currently: %s', $this->max, $count),
                $filename,
                $number + 1,
                $line
            );
        }

        return NullViolation::create();
    }
}
