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

class ArgumentTypeMatchName extends AbstractRule implements LineContentRule, Configurable
{
    /** @var array<array{type: string, name:string}> */
    private array $arguments;

    public function configureOptions(OptionsResolver $resolver): OptionsResolver
    {
        $resolver->setDefault('arguments', function (OptionsResolver $connResolver) {
            $connResolver
                ->setPrototype(true)
                ->setRequired(['type', 'name']);
        });

        return $resolver;
    }

    public function setOptions(array $options): void
    {
        $resolver = $this->configureOptions(new OptionsResolver());

        $resolvedOptions = $resolver->resolve($options);

        $this->arguments = $resolvedOptions['arguments'];
    }

    public static function getGroups(): array
    {
        return [RuleGroup::Symfony()];
    }

    public function check(Lines $lines, int $number): ?string
    {
        $lines->seek($number);
        $line = $lines->current()->clean();

        $messageParts = [];
        foreach ($this->arguments as $argument) {
            // This regex match argument type with bad argument name
            $regex = sprintf(
                '/%s \$(?!%s)/',
                $argument['type'],
                $argument['name']
            );

            if ($line->match($regex)) {
                $messageParts[] = sprintf(
                    'Please name the argument "%s $%s"',
                    $argument['type'],
                    $argument['name']
                );
            }
        }

        return $messageParts ? implode('. ', $messageParts) : null;
    }
}
