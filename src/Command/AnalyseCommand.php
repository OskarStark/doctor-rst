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

use App\Analyser\MemoizingAnalyser;
use App\Formatter\Registry as FormatterRegistry;
use App\Handler\Registry;
use App\Rule\Configurable;
use App\Rule\Rule;
use App\Value\AnalyserResult;
use App\Value\ExcludedViolationList;
use App\Value\FileResult;
use App\Value\RuleGroup;
use App\Value\RuleName;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

class AnalyseCommand extends Command
{
    protected static $defaultName = 'analyse';

    private Registry $registry;

    /** @var Rule[] */
    private array $rules = [];

    private MemoizingAnalyser $analyser;
    private FormatterRegistry $formatterRegistry;

    public function __construct(Registry $registry, MemoizingAnalyser $analyser, FormatterRegistry $formatterRegistry)
    {
        $this->registry = $registry;
        $this->analyser = $analyser;
        $this->formatterRegistry = $formatterRegistry;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Analyse *.rst files')
            ->addArgument('dir', InputArgument::OPTIONAL, 'Directory', '.')
            ->addOption('rule', 'r', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Which rule should be applied?')
            ->addOption('group', 'g', InputOption::VALUE_REQUIRED, 'Which groups should be used?')
            ->addOption('short', null, InputOption::VALUE_NONE, 'Do not output valid files.')
            ->addOption('error-format', null, InputOption::VALUE_OPTIONAL, 'Format in which to print the result of the analysis', 'console');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $dir = $input->getArgument('dir');
        \assert(\is_string($dir));

        if (!$analyseDir = realpath($dir)) {
            $io->error(sprintf('Could not find directory: %s', $dir));

            return 1;
        }

        $io->text(sprintf('Analyse *.rst(.inc) files in: <info>%s</info>', $analyseDir));

        if (!is_file($configFile = $analyseDir.'/.doctor-rst.yaml')) {
            $io->error(sprintf('Could not find config file: %s', $configFile));

            return 1;
        }

        $io->text(sprintf('Used config file:             <info>%s</info>', $configFile));
        $io->newLine();

        $config = Yaml::parseFile($configFile);

        foreach ($config['rules'] as $name => $options) {
            /** @var Rule[] $rules */
            $rules = $this->registry->getRulesByName(RuleName::fromString($name));

            foreach ($rules as $rule) {
                if ($rule instanceof Configurable) {
                    $rule->setOptions($options ?? []);
                }

                $this->rules[] = $rule;
            }
        }

        if (!empty($input->getOption('rule') && !empty($input->getOption('group')))) {
            $io->error('You can only provide "rule" or "group"!');

            return 1;
        }

        if (\is_array($input->getOption('rule')) && !empty($input->getOption('rule'))) {
            foreach ($input->getOption('rule') as $rule) {
                $this->rules[] = $this->registry->getRule(RuleName::fromString($rule));
            }
        }

        if (!empty($input->getOption('group'))) {
            $group = $input->getOption('group');

            \assert(\is_string($group));

            $this->rules = $this->registry->getRulesByGroup(RuleGroup::fromString($group));
        }

        if (empty($this->rules)) {
            $io->warning('No rules selected!');

            return 1;
        }

        $errorFormat = $input->getOption('error-format');
        \assert(\is_string($errorFormat));

        $showValidFiles = $input->getOption('short') ? false : true;

        $finder = new Finder();
        $finder->files()->name(['*.rst', '*.rst.inc'])->in($analyseDir);

        $fileResults = [];
        foreach ($finder as $file) {
            $fileResults[] = new FileResult(
                $file,
                new ExcludedViolationList(
                    $config['whitelist'] ?? [],
                    $this->analyser->analyse($file, $this->rules)
                )
            );
        }
        $analyserResult = new AnalyserResult($fileResults);

        $this->analyser->write();

        $this->formatterRegistry
            ->get($errorFormat)
            ->format($io, $analyserResult, $analyseDir, $showValidFiles);

        return $analyserResult->hasViolations() ? 1 : 0;
    }
}
