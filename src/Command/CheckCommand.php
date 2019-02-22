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

use App\Handler\RulesHandler;
use App\Rule\Rule;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Yaml\Yaml;

class CheckCommand extends Command
{
    protected static $defaultName = 'check';

    /** @var SymfonyStyle */
    private $io;

    /** @var array */
    private $violations;

    /** @var RulesHandler */
    private $rulesHandler;

    /** @var Rule[] */
    private $rules = [];

    /** @var bool */
    private $dryRun = false;

    public function __construct(RulesHandler $rulesHandler, ?string $name = null)
    {
        $this->rulesHandler = $rulesHandler;

        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setDescription('Check *.rst files')
            ->addArgument('dir', InputArgument::OPTIONAL, 'Directory', '.')
            ->addOption('rule', 'r', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Which rule should be applied?')
            ->addOption('group', 'g', InputOption::VALUE_REQUIRED, 'Which groups should be used?')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Dry-Run')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);

        $this->io->title(sprintf('Check *.rst files in: <info>%s</info>', $input->getArgument('dir')));

        if (!empty($input->getOption('rule') && !empty($input->getOption('group')))) {
            $this->io->error('You can only provide "rule" or "group"!');

            return 1;
        }

        if (\is_array($input->getOption('rule')) && !empty($input->getOption('rule'))) {
            foreach ($input->getOption('rule') as $rule) {
                $this->rules[] = $this->rulesHandler->getRule($rule);
            }
        }

        if (!empty($input->getOption('group'))) {
            $this->rules = $this->rulesHandler->getRulesByGroup($input->getOption('group'));
        }

        if (empty($this->rules)) {
            $this->io->warning('No rules selected!');

            return;
        }

        if ($input->getOption('dry-run')) {
            $this->dryRun = true;
        }

        $finder = new Finder();
        $finder->files()->name(['*.rst', '*.rst.inc'])->in($input->getArgument('dir'));

        foreach ($finder as $file) {
            $this->checkFile($file);
        }

        if ($this->violations) {
            $this->io->warning('Found invalid files!');
        } else {
            $this->io->success('All files are valid!');
        }

        return $this->violations ? 1 : 0;
    }

    private function checkFile(SplFileInfo $file)
    {
        $this->io->writeln($file->getPathname());

        $lines = new \ArrayIterator(file($file->getRealPath()));

        $violations = [];
        foreach ($lines as $no => $line) {
            if (empty($line)) {
                continue;
            }

            /** @var Rule $rule */
            foreach ($this->rules as $rule) {
                $violation = $rule->check($lines, $no);

                if (!empty($violation)) {
                    $violations[] = [
                        $rule::getName(),
                        $violation,
                        $no + 1,
                        trim($line),
                    ];
                }
            }
        }

        $violations = $this->filterWhitelistedViolations($violations);

        if (!empty($violations)) {
            $this->violations = true;

            $this->io->table(['Rule', 'Violation', 'Line', 'Extracted line from file'], $violations);
        }
    }

    private function filterWhitelistedViolations(array $violations): array
    {
        $config = Yaml::parseFile(__DIR__.'/../../dummy/.doctor-rst.yaml');

        foreach ($violations as $key => $violation) {
            foreach ($config['whitelist']['regex'] as $pattern) {
                if (preg_match($pattern, $violation[3])) {
                    unset($violations[$key]);

                    break;
                }
            }

            foreach ($config['whitelist']['lines'] as $line) {
                if ($line === $violation[3]) {
                    unset($violations[$key]);

                    break;
                }
            }
        }

        return $violations;
    }
}
