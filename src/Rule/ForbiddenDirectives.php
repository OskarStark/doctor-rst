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
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

#[Description('Make sure forbidden directives are not used')]
final class ForbiddenDirectives extends AbstractRule implements Configurable, LineContentRule
{
    use DirectiveTrait;

    /**
     * @var array<array{directive: string, replacements: ?string[]}>
     */
    private array $forbiddenDirectives;

    public function configureOptions(OptionsResolver $resolver): OptionsResolver
    {
        $resolver
            ->setRequired('directives')
            ->setAllowedTypes('directives', 'array')
            ->setNormalizer(
                'directives',
                /** @phpstan-ignore-next-line */
                static fn (Options $options, array $directives): array => \array_map(static function (array|string $directive) {
                    /** @var array<array{directive: string, replacements: ?string[]}>|string $directive */
                    if (!\is_array($directive)) {
                        $directive = ['directive' => $directive];
                    }

                    if (isset($directive['replacements']) && \is_string($directive['replacements'])) {
                        $directive['replacements'] = [$directive['replacements']];
                    }

                    if (!isset($directive['directive']) || !\is_string($directive['directive'])) {
                        throw new InvalidOptionsException('A directive in "directives" is invalid. It needs at least a "directive" key with a string value');
                    }

                    return $directive;
                }, $directives),
            )
            ->setDefault('directives', []);

        return $resolver;
    }

    public function setOptions(array $options): void
    {
        $resolver = $this->configureOptions(new OptionsResolver());

        $resolvedOptions = $resolver->resolve($options);

        /** @phpstan-ignore-next-line */
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
            if (RstParser::directiveIs($line, $forbiddenDirective['directive'])) {
                $message = \sprintf('Please don\'t use directive "%s" anymore', $line->raw()->toString());

                if (isset($forbiddenDirective['replacements'])) {
                    $message = \sprintf(
                        '%s, use "%s" instead',
                        $message,
                        \implode('" or "', $forbiddenDirective['replacements']),
                    );
                }

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
