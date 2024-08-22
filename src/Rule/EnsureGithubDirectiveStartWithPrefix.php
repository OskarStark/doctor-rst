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

use App\Value\Lines;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EnsureGithubDirectiveStartWithPrefix extends AbstractRule implements Configurable, LineContentRule
{
    private string $prefix;

    public function configureOptions(OptionsResolver $resolver): OptionsResolver
    {
        $resolver
            ->setRequired('prefix')
            ->setAllowedTypes('prefix', 'string');

        return $resolver;
    }

    public function setOptions(array $options): void
    {
        $resolver = $this->configureOptions(new OptionsResolver());

        $resolvedOptions = $resolver->resolve($options);

        $this->prefix = $resolvedOptions['prefix'];
    }

    public function check(Lines $lines, int $number, string $filename): ViolationInterface
    {
        $lines->seek($number);
        $line = $lines->current();

        if ($line->clean()->match('/:(method|class|namespace):`.*`/')
            && !$line->clean()->match('/:(method|class|namespace):`.*'.$this->prefix.'\\\\.*`/')) {
            $message = \sprintf(
                'Please only use "%s" base namespace with Github directive',
                $this->prefix,
            );

            return Violation::from(
                $message,
                $filename,
                $number + 1,
                $line,
            );
        }

        return NullViolation::create();
    }
}
