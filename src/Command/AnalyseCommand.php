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

use App\Handler\Registry;
use App\Rst\RstParser;
use App\Rule\Configurable;
use App\Rule\Rule;
use App\Value\Lines;
use App\Value\RuleGroup;
use App\Value\RuleName;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Yaml\Yaml;
use Symfony\Contracts\Service\ResetInterface;

class AnalyseCommand extends Command
{
    protected static $defaultName = 'analyse';

    /** @var SymfonyStyle */
    private $io;

    /** @var Registry */
    private $registry;

    /** @var Rule[] */
    private $rules = [];

    /** @var bool */
    private $short = false;

    /** @var string */
    private $dir;

    /** @var mixed */
    private $config;

    public function __construct(Registry $registry, ?string $name = null)
    {
        $this->registry = $registry;

        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Analyse *.rst files')
            ->addArgument('dir', InputArgument::OPTIONAL, 'Directory', '.')
            ->addOption('rule', 'r', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Which rule should be applied?')
            ->addOption('group', 'g', InputOption::VALUE_REQUIRED, 'Which groups should be used?')
            ->addOption('short', null, InputOption::VALUE_NONE, 'Do not output valid files.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);

        $dir = $input->getArgument('dir');
        \assert(\is_string($dir));

        if (!$realpath = realpath($dir)) {
            $this->io->error(sprintf('Could not find directory: %s', $dir));

            return 1;
        }

        $this->dir = $realpath;

        $this->io->text(sprintf('Analyse *.rst(.inc) files in: <info>%s</info>', $this->dir));

        if (!is_file($configFile = $this->dir.'/.doctor-rst.yaml')) {
            $this->io->error(sprintf('Could not find config file: %s', $configFile));
        }

        $this->io->text(sprintf('Used config file:             <info>%s</info>', $configFile));
        $this->io->newLine();

        $this->config = Yaml::parseFile($configFile);

        foreach ($this->config['rules'] as $name => $options) {
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
            $this->io->error('You can only provide "rule" or "group"!');

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
            $this->io->warning('No rules selected!');

            return 1;
        }

        if ($input->getOption('short')) {
            $this->short = true;
        }

        $finder = new Finder();
        $finder->files()->name(['*.rst', '*.rst.inc'])->in($this->dir);

        $violatedFiles = 0;
        foreach ($finder as $file) {
            $violatedFiles = $violatedFiles + $this->checkFile($file);
        }

        if ($violatedFiles > 0) {
            $this->io->warning(sprintf(
                'Found "%s" invalid %s!',
                $violatedFiles,
                1 === $violatedFiles ? 'file' : 'files'
            ));
        } else {
            $this->io->success('All files are valid!');
        }

        return $violatedFiles > 0 ? 1 : 0;
    }

    private function checkFile(SplFileInfo $file): int
    {
        $realpath = $file->getRealPath();
        if (false === $realpath) {
            throw new \RuntimeException(sprintf(
                'Cannot get real path for file: %s',
                (string) $file->getRealPath()
            ));
        }

        $content = file($realpath);

        if (false == $content) {
            throw new \RuntimeException(sprintf(
                'Cannot parse file: %s',
                (string) $file->getRealPath()
            ));
        }

        $lines = Lines::fromArray($content);

        $violations = [];
        foreach ($lines->toIterator() as $no => $line) {
            \assert(\is_int($no));

            /** @var Rule $rule */
            foreach ($this->rules as $rule) {
                if (!$rule::runOnlyOnBlankline() && RstParser::isBlankLine($line)) {
                    continue;
                }

                if (Rule::TYPE_FILE === $rule::getType() && $no > 0) {
                    continue;
                }
                $violation = $rule->check($lines, $no);

                if (null !== $violation) {
                    $violations[] = [
                        $rule::getName()->asString(),
                        $violation,
                        $no + 1,
                        Rule::TYPE_FILE === $rule::getType() ? '' : trim($line),
                    ];
                }

                if ($rule instanceof ResetInterface) {
                    $rule->reset();
                }
            }
        }

        $violations = $this->filterWhitelistedViolations($violations);
        $hasViolations = !empty($violations);

        if (!$this->short || $hasViolations) {
            $this->io->writeln(sprintf(
                '%s %s',
                ltrim(str_replace($this->dir, '', $file->getPathname()), '/'),
                $hasViolations ? sprintf('<fg=red;options=bold>%s</>', "\xE2\x9C\x98" /* HEAVY BALLOT X (U+2718) */) : sprintf('<fg=green;options=bold>%s</>', "\xE2\x9C\x94" /* HEAVY CHECK MARK (U+2714) */)
            ));
        }

        if ($hasViolations) {
            foreach ($violations as $violation) {
                $this->io->writeln(sprintf(
                    '<comment>%s</comment>: %s',
                    str_pad((string) $violation[2], 5, ' ', STR_PAD_LEFT),
                    $violation[1]
                ));
                if (!empty($violation[3])) {
                    $this->io->writeln(sprintf(
                        '   <info>-></info>  %s',
                        $violation[3]
                    ));
                }
            }
            $this->io->newLine();

            return 1;
        }

        return 0;
    }

    private function filterWhitelistedViolations(array $violations): array
    {
        if (isset($this->config['whitelist'])) {
            foreach ($violations as $key => $violation) {
                if (isset($this->config['whitelist']['regex'])) {
                    foreach ($this->config['whitelist']['regex'] as $pattern) {
                        if (preg_match($pattern, $violation[3])) {
                            unset($violations[$key]);

                            break;
                        }
                    }
                }

                if (isset($this->config['whitelist']['lines'])) {
                    foreach ($this->config['whitelist']['lines'] as $line) {
                        if ($line === $violation[3]) {
                            unset($violations[$key]);

                            break;
                        }
                    }
                }
            }
        }

        return $violations;
    }
}
