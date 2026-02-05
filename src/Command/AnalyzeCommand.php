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

use App\Analyzer\MemoizingAnalyzer;
use App\Formatter\Registry as FormatterRegistry;
use App\Handler\Registry;
use App\Rule\Configurable;
use App\Value\AnalyzerResult;
use App\Value\ExcludedViolationList;
use App\Value\FileResult;
use App\Value\RuleGroup;
use App\Value\RuleName;
use App\Value\RulesConfiguration;
use OndraM\CiDetector\CiDetector;
use OndraM\CiDetector\Exception\CiNotDetectedException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

#[AsCommand('analyze', null, ['analyse'])]
class AnalyzeCommand extends Command
{
    private readonly RulesConfiguration $rulesConfiguration;

    public function __construct(
        private readonly Registry $registry,
        private readonly MemoizingAnalyzer $analyzer,
        private readonly FormatterRegistry $formatterRegistry,
    ) {
        $this->rulesConfiguration = new RulesConfiguration();

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('dir', InputArgument::OPTIONAL, 'Directory', '.')
            ->addOption('rule', 'r', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Which rule should be applied?')
            ->addOption('group', 'g', InputOption::VALUE_REQUIRED, 'Which groups should be used?')
            ->addOption('short', null, InputOption::VALUE_NONE, 'Do not output valid files.')
            ->addOption('error-format', null, InputOption::VALUE_OPTIONAL, 'Format in which to print the result of the analysis. Can be: "detect", "console", "github"', 'detect');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $dir = $input->getArgument('dir');
        \assert(\is_string($dir));

        if (!$analyzeDir = realpath($dir)) {
            $io->error(\sprintf('Could not find directory: %s', $dir));

            return Command::FAILURE;
        }

        $io->text(\sprintf('Analyze *.rst(.inc) files in: <info>%s</info>', $analyzeDir));

        if (!is_file($configFile = $analyzeDir.'/.doctor-rst.yaml')) {
            $io->error(\sprintf('Could not find config file: %s', $configFile));

            return Command::FAILURE;
        }

        $io->text(\sprintf('Used config file:             <info>%s</info>', $configFile));
        $io->newLine();

        $config = Yaml::parseFile($configFile);
        /**
         * @var array{
         *     rules: array<string, null|array<string, mixed>>,
         *     exclude_rule_for_file?: null|array<array{path?: ?string, rule_name?: ?string}>,
         *     whitelist?: null|array{regex?: string[], lines?: string[]},
         * } $config
         */
        $rules = $config['rules'];

        foreach ($rules as $name => $options) {
            $rules = $this->registry->getRulesByName(RuleName::fromString($name));

            foreach ($rules as $rule) {
                if ($rule instanceof Configurable) {
                    $rule->setOptions($options ?? []);
                }

                $this->rulesConfiguration->addRuleForAll($rule);
            }
        }

        $excludeRuleForFileConfiguration = $config['exclude_rule_for_file'] ?? [];

        foreach ($excludeRuleForFileConfiguration as $exclusionConfiguration) {
            $filePath = $exclusionConfiguration['path'] ?? null;
            \assert(\is_string($filePath));

            $ruleName = $exclusionConfiguration['rule_name'] ?? null;
            \assert(\is_string($ruleName));

            $rules = $this->registry->getRulesByName(RuleName::fromString($ruleName));
            $this->rulesConfiguration->excludeRulesForFilePath($filePath, $rules);
        }

        if ($input->getOption('rule') && !empty($input->getOption('group'))) {
            $io->error('You can only provide "rule" or "group"!');

            return Command::FAILURE;
        }

        if (\is_array($input->getOption('rule')) && $input->getOption('rule') !== []) {
            foreach ($input->getOption('rule') as $rule) {
                /** @var string $rule */
                $this->rulesConfiguration->addRuleForAll($this->registry->getRule(RuleName::fromString($rule)));
            }
        }

        if (!empty($input->getOption('group'))) {
            $group = $input->getOption('group');

            \assert(\is_string($group));

            $rules = $this->registry->getRulesByGroup(RuleGroup::fromString($group));
            $this->rulesConfiguration->setRulesForAll($rules);
        }

        if (!$this->rulesConfiguration->hasRulesForAll()) {
            $io->warning('No rules selected!');

            return Command::FAILURE;
        }

        $errorFormat = $input->getOption('error-format');
        \assert(\is_string($errorFormat));

        if ('detect' === $errorFormat) {
            $ciDetector = new CiDetector();
            $errorFormat = 'console';

            try {
                $ci = $ciDetector->detect();

                if (CiDetector::CI_GITHUB_ACTIONS === $ci->getCiName()) {
                    $errorFormat = 'github';
                }
            } catch (CiNotDetectedException) {
                // pass and use default
            }
        }

        $showValidFiles = !(bool) $input->getOption('short');

        $finder = new Finder();
        $finder->files()->name(['*.rst', '*.rst.inc'])->in($analyzeDir)->exclude('vendor');

        $whitelistConfig = $config['whitelist'] ?? [];

        $fileResults = [];

        foreach ($finder as $file) {
            if ($output->isVeryVerbose()) {
                $output->writeln('Analyze '.$file->getRealPath());
            }

            $rules = $this->rulesConfiguration->getRulesForFilePath($file->getRelativePathname());
            $fileResults[] = new FileResult(
                $file,
                new ExcludedViolationList(
                    $whitelistConfig,
                    $this->analyzer->analyze($file, $rules),
                ),
            );
        }

        $analyzerResult = new AnalyzerResult($fileResults, $whitelistConfig);

        $this->analyzer->write();

        $this->formatterRegistry
            ->get($errorFormat)
            ->format($io, $analyzerResult, $analyzeDir, $showValidFiles);

        return $analyzerResult->hasViolations() ? 1 : 0;
    }
}
