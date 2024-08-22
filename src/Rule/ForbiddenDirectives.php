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
use App\Rst\RstParser;
use App\Traits\DirectiveTrait;
use App\Value\Lines;
use App\Value\NullViolation;
use App\Value\RuleGroup;
use App\Value\Violation;
use App\Value\ViolationInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

#[Description('Make sure forbidden directives are not used')]
class ForbiddenDirectives extends AbstractRule implements Configurable, LineContentRule
{
    use DirectiveTrait;

    /**
     * @var array<string>
     */
    private array $forbiddenDirectives;

    public function configureOptions(OptionsResolver $resolver): OptionsResolver
    {
        $resolver
            ->setRequired('directives')
            ->setAllowedTypes('directives', 'string[]')
            ->setDefault('directives', []);

        return $resolver;
    }

    public function setOptions(array $options): void
    {
        $resolver = $this->configureOptions(new OptionsResolver());

        $resolvedOptions = $resolver->resolve($options);

        $this->forbiddenDirectives = $resolvedOptions['directives'];
    }

    public static function getGroups(): array
    {
        return [RuleGroup::Symfony()];
    }

    public function check(Lines $lines, int $number, string $filename): ViolationInterface
    {
        $lines->seek($number);
        $line = $lines->current();

        foreach ($this->forbiddenDirectives as $forbiddenDirective) {
            if (RstParser::directiveIs($line, $forbiddenDirective)) {
                $message = \sprintf('Please don\'t use directive "%s" anymore', $line->raw()->toString());

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
