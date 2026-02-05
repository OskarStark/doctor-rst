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

use App\Attribute\Rule\Description;
use App\Traits\DirectiveTrait;
use App\Value\Lines;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

#[Description('Make sure argument variable name match for type')]
final class ArgumentVariableMustMatchType extends AbstractRule implements Configurable, LineContentRule
{
    use DirectiveTrait;

    /**
     * @var array<array{type: string, name: string}>
     */
    private array $arguments;

    public function configureOptions(OptionsResolver $resolver): OptionsResolver
    {
        $resolver
            ->setRequired('arguments')
            ->setAllowedTypes('arguments', 'array')
            ->setDefault('arguments', static function (OptionsResolver $connResolver): void {
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

        /** @phpstan-ignore-next-line */
        $this->arguments = $resolvedOptions['arguments'];
    }

    public function check(Lines $lines, int $number, string $filename): ViolationInterface
    {
        $lines->seek($number);
        $line = $lines->current();

        if (!$this->inPhpCodeBlock($lines, $number)) {
            return NullViolation::create();
        }

        $messageParts = [];

        foreach ($this->arguments as $argument) {
            // This regex match argument type with bad argument name
            $regex = \sprintf(
                '/%s \$(?!%s)(?<actualName>[a-z-A-Z\$]+)/',
                $argument['type'],
                $argument['name'],
            );
            /** @var array{actualName?: string} $match */
            $match = $line->clean()->match($regex);

            if ($match) {
                $messageParts[] = \sprintf(
                    'Please rename "$%s" to "$%s"',
                    $match['actualName'],
                    $argument['name'],
                );
            }
        }

        $message = [] !== $messageParts ? implode('. ', $messageParts) : null;

        if (null === $message) {
            return NullViolation::create();
        }

        return Violation::from(
            $message,
            $filename,
            $number + 1,
            $line,
        );
    }
}
