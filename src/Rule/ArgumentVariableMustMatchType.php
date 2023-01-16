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

use App\Annotations\Rule\Description;
use App\Rst\RstParser;
use App\Value\Lines;
use App\Value\RuleGroup;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @Description("Make sure argument variable name match for type")
 */
class ArgumentVariableMustMatchType extends AbstractRule implements LineContentRule, Configurable
{
    /** @var array<array{type: string, name: string}> */
    private array $arguments;

    public function configureOptions(OptionsResolver $resolver): OptionsResolver
    {
        $resolver
            ->setRequired('arguments')
            ->setAllowedTypes('arguments', 'array')
            ->setDefault('arguments', function (OptionsResolver $connResolver): void {
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
        $line = $lines->current();

        if (!RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_PHP)
            && !RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_PHP_ANNOTATIONS)
            && !RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_PHP_ATTRIBUTES)
            && !RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_PHP_SYMFONY)
        ) {
            return null;
        }

        $lines->next();

        $messageParts = [];

        while ($lines->valid()
            && !$lines->current()->isDirective()
        ) {
            $line = $lines->current()->clean();

            foreach ($this->arguments as $argument) {
                // This regex match argument type with bad argument name
                $regex = sprintf(
                    '/%s \$(?!%s)(?<actualName>[a-z-A-Z\$]+)/',
                    $argument['type'],
                    $argument['name']
                );
                $match = $line->match($regex);

                if ($match) {
                    $messageParts[] = sprintf(
                        'Please rename "$%s" to "$%s"',
                        $match['actualName'],
                        $argument['name']
                    );
                }
            }

            $lines->next();
        }

        return $messageParts ? implode('. ', $messageParts) : null;
    }
}
