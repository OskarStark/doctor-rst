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

namespace App\Command;

use App\Annotations\Rule as RuleAnnotation;
use App\Handler\Registry;
use App\Rule\CheckListRule;
use App\Rule\Configurable;
use App\Rule\Rule;
use App\Value\RuleGroup;
use Doctrine\Common\Annotations\Reader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\OptionsResolver\Debug\OptionsResolverIntrospector;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RulesCommand extends Command
{
    protected static $defaultName = 'rules';

    private SymfonyStyle $io;
    private Registry $registry;
    private Reader $annotationReader;

    public function __construct(Registry $registry, Reader $annotationReader, ?string $name = null)
    {
        $this->registry = $registry;
        $this->annotationReader = $annotationReader;

        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->setDescription('List available rules')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);

        $this->io->writeln('# Rules Overview');
        $this->io->newLine();

        $rules = $this->registry->getRawRules();

        if ([] === $rules) {
            $this->io->warning('No rules available!');

            return 1;
        }

        foreach ($rules as $rule) {
            $this->io->writeln(sprintf(
                '* [%s](#%s)%s',
                $rule::getName()->toString(),
                $rule::getName()->toString(),
                $rule::isExperimental() ? ' :exclamation:' : ''
            ));
        }

        foreach ($rules as $rule) {
            $this->rule($rule);
        }

        return 0;
    }

    private function rule(Rule $rule): void
    {
        /** @var RuleAnnotation\Description $description */
        $description = $this->annotationReader->getClassAnnotation(
            new \ReflectionClass(\get_class($rule)),
            RuleAnnotation\Description::class
        );

        $this->io->writeln(sprintf('## `%s`', $rule::getName()->toString()));
        $this->io->newLine();

        if (null !== $description) {
            $this->io->writeln(sprintf(
                '  > _%s_',
                $description->value
            ));
            $this->io->newLine();
        }

        if ([] !== $rule::getGroups()) {
            $groupNames = array_map(static function (RuleGroup $group): string {
                return $group->name();
            }, $rule::getGroups());

            $this->io->writeln(sprintf('#### Groups [`%s`]', implode('`, `', $groupNames)));
            $this->io->newLine();
        }

        if ($rule instanceof Configurable) {
            $this->io->writeln('#### Configuration options');
            $this->io->newLine();

            /** @var array{name: string, required: bool, types: array, default: mixed} $options */
            $options = [];

            $hasAnOptionWithDefaultValue = false;

            $resolver = $rule->configureOptions(new OptionsResolver());
            $introspector = new OptionsResolverIntrospector($resolver);
            foreach ($resolver->getDefinedOptions() as $option) {
                $required = false;
                $default = null;

                $allowedTypes = $introspector->getAllowedTypes($option);
                if ($resolver->isRequired($option)) {
                    $required = true;

                    if ($resolver->hasDefault($option)) {
                        $hasAnOptionWithDefaultValue = true;
                        $required = false;

                        $default = $introspector->getDefault($option);
                    }
                }

                $options[] = [
                    'name' => $option,
                    'required' => $required,
                    'types' => $allowedTypes,
                    'default' => $default,
                ];
            }

            if ([] !== $options) {
                if ($hasAnOptionWithDefaultValue) {
                    $this->io->writeln('Name | Required | Allowed Types | Default');
                    $this->io->writeln('--- | --- | --- | ---');

                    foreach ($options as $option) {
                        if (null === $defaultValue = $option['default']) {
                            $default = '';
                        } else {
                            if (\is_array($defaultValue)) {
                                $defaultValue = '[]';
                            }
                            $default = sprintf('`%s`', $defaultValue);
                        }

                        $this->io->writeln(sprintf(
                            '%s | %s | %s | %s',
                            sprintf('`%s`', $option['name']),
                            sprintf('`%s`', $option['required'] ? 'true' : 'false'),
                            sprintf('%s', [] === $option['types'] ? '' : '`'.implode('`, `', $option['types']).'`'),
                            $default
                        ));
                    }
                } else {
                    $this->io->writeln('Name | Required');
                    $this->io->writeln('--- | ---');

                    foreach ($options as $option) {
                        $this->io->writeln(sprintf(
                            '%s | %s | %s',
                            sprintf('`%s`', $option['name']),
                            sprintf('`%s`', $option['required'] ? 'true' : 'false'),
                            sprintf('%s', [] === $option['types'] ? '' : '`'.implode('`, `', $option['types']).'`'),
                        ));
                    }
                }

                $this->io->newLine();
            }
        }

        if ($rule instanceof CheckListRule && !empty($rule::getList())) {
            $this->io->writeln('#### Checks');
            $this->io->newLine();
            $this->io->writeln('Pattern | Message');
            $this->io->writeln('--- | ---');
            foreach ($rule::getList() as $pattern => $message) {
                $this->io->writeln(sprintf('`%s` | %s', str_replace('|', '\|', $pattern), $message ?: $rule->getDefaultMessage()));
            }
            $this->io->newLine();
        }

        /** @var RuleAnnotation\ValidExample $validExample */
        $validExample = $this->annotationReader->getClassAnnotation(
            new \ReflectionClass(\get_class($rule)),
            RuleAnnotation\ValidExample::class
        );

        /** @var RuleAnnotation\InvalidExample $invalidExample */
        $invalidExample = $this->annotationReader->getClassAnnotation(
            new \ReflectionClass(\get_class($rule)),
            RuleAnnotation\InvalidExample::class
        );

        if (null !== $validExample) {
            $this->renderExamples('##### Valid Examples :+1:', \is_array($validExample->value) ? $validExample->value : [$validExample->value]);
        }

        if (null !== $invalidExample) {
            $this->renderExamples('##### Invalid Examples :-1:', \is_array($invalidExample->value) ? $invalidExample->value : [$invalidExample->value]);
        }
    }

    /**
     * @param string[] $examples
     */
    private function renderExamples(string $headline, array $examples): void
    {
        $this->io->writeln($headline);

        foreach ($examples as $example) {
            $this->io->newLine();
            $this->io->writeln('```rst');
            $this->io->writeln($example);
            $this->io->writeln('```');
        }

        $this->io->newLine();
    }
}
