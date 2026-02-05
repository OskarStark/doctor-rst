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

namespace App\Command;

use App\Attribute\Rule\Description;
use App\Attribute\Rule\InvalidExample;
use App\Attribute\Rule\ValidExample;
use App\Handler\Registry;
use App\Rule\CheckListRule;
use App\Rule\Configurable;
use App\Rule\Rule;
use App\Value\RuleGroup;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\OptionsResolver\Debug\OptionsResolverIntrospector;
use Symfony\Component\OptionsResolver\OptionsResolver;

#[AsCommand('rules', 'List available rules')]
class RulesCommand extends Command
{
    private SymfonyStyle $io;

    public function __construct(
        private readonly Registry $registry,
        ?string $name = null,
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);

        $this->io->writeln('# Rules Overview');
        $this->io->newLine();

        $rules = $this->registry->getRawRules();

        if ([] === $rules) {
            $this->io->warning('No rules available!');

            return Command::FAILURE;
        }

        foreach ($rules as $rule) {
            $this->io->writeln(\sprintf(
                '* [%s](#%s)%s',
                $rule::getName()->toString(),
                $rule::getName()->toString(),
                $rule::isExperimental() ? ' :exclamation:' : '',
            ));
        }

        foreach ($rules as $rule) {
            $this->rule($rule);
        }

        return Command::SUCCESS;
    }

    private function rule(Rule $rule): void
    {
        $reflectionClass = new \ReflectionClass($rule::class);

        $description = null;

        if ($descriptions = $reflectionClass->getAttributes(Description::class)) {
            /** @var Description $description */
            $description = $descriptions[0]->newInstance();
        }

        $this->io->writeln(\sprintf('## `%s`', $rule::getName()->toString()));
        $this->io->newLine();

        if (null !== $description) {
            $this->io->writeln(\sprintf(
                '  > _%s_',
                $description->value,
            ));
            $this->io->newLine();
        }

        if ([] !== $rule::getGroups()) {
            $groupNames = array_map(static fn (RuleGroup $group): string => $group->name(), $rule::getGroups());

            $this->io->writeln(\sprintf('#### Groups [`%s`]', implode('`, `', $groupNames)));
            $this->io->newLine();
        }

        if ($rule instanceof Configurable) {
            $this->io->writeln('#### Configuration options');
            $this->io->newLine();

            /** @var array<array{name: string, required: bool, types: array, default: mixed}> $options */
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

                            /** @phpstan-ignore-next-line */
                            $default = \sprintf('`%s`', $defaultValue);
                        }

                        $this->io->writeln(\sprintf(
                            '%s | %s | %s | %s',
                            \sprintf('`%s`', $option['name']),
                            \sprintf('`%s`', $option['required'] ? 'true' : 'false'),
                            [] === $option['types'] ? '' : '`'.implode('`, `', $option['types']).'`',
                            $default,
                        ));
                    }
                } else {
                    $this->io->writeln('Name | Required');
                    $this->io->writeln('--- | ---');

                    foreach ($options as $option) {
                        $this->io->writeln(\sprintf(
                            '%s | %s | %s',
                            \sprintf('`%s`', $option['name']),
                            \sprintf('`%s`', $option['required'] ? 'true' : 'false'),
                            [] === $option['types'] ? '' : '`'.implode('`, `', $option['types']).'`',
                        ));
                    }
                }

                $this->io->newLine();
            }
        }

        if ($rule instanceof CheckListRule && [] !== $rule::getList()) {
            $this->io->writeln('#### Checks');
            $this->io->newLine();
            $this->io->writeln('Pattern | Message');
            $this->io->writeln('--- | ---');

            foreach ($rule::getList() as $pattern => $message) {
                $this->io->writeln(\sprintf('`%s` | %s', str_replace('|', '\|', (string) $pattern), $message ?: $rule->getDefaultMessage()));
            }

            $this->io->newLine();
        }

        $validExamples = [];

        foreach ($reflectionClass->getAttributes(ValidExample::class) as $attribute) {
            $validExamples[] = $attribute->newInstance()->value;
        }

        if ([] !== $validExamples) {
            $this->renderExamples('##### Valid Examples :+1:', $validExamples);
        }

        $invalidExamples = [];

        foreach ($reflectionClass->getAttributes(InvalidExample::class) as $attribute) {
            $invalidExamples[] = $attribute->newInstance()->value;
        }

        if ([] !== $invalidExamples) {
            $this->renderExamples('##### Invalid Examples :-1:', $invalidExamples);
        }

        $this->renderReferences($reflectionClass->getName(), $reflectionClass->getShortName());
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

    private function renderReferences(string $className, string $classShortName): void
    {
        $this->io->writeln('#### References');
        $this->io->newLine();

        $classPath = \sprintf(
            'src/Rule/%s.php',
            $classShortName,
        );
        $ruleLink = self::renderGithubLink($className, $classPath);
        $this->io->writeln(
            \sprintf(
                '- Rule class: %s',
                $ruleLink,
            ),
        );

        $testName = \sprintf(
            'App\Tests\Rule\\%sTest',
            $classShortName,
        );

        if (class_exists($testName)) {
            $testPath = \sprintf(
                'tests/Rule/%sTest.php',
                $classShortName,
            );
            $testLink = self::renderGithubLink($testName, $testPath);
            $this->io->writeln(
                \sprintf(
                    '- Test class: %s',
                    $testLink,
                ),
            );
        }

        $this->io->newLine();
    }

    private static function renderGithubLink(string $name, string $relativeFilePath): string
    {
        return \sprintf(
            '[%s](%s%s)',
            $name,
            'https://github.com/OskarStark/doctor-rst/blob/develop/',
            $relativeFilePath,
        );
    }
}
