<?php

declare(strict_types=1);

/*
 * This file is part of the rst-checker.
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

class CheckCommand extends Command
{
    protected static $defaultName = 'check';

    /** @var SymfonyStyle */
    private $io;

    /** @var array */
    private $violations;

    /** @var RulesHandler */
    private $rulesHandler;

    /** @var bool */
    private $dryRun = false;

    public function __construct(RulesHandler $rulesHandler, ?string $name = null)
    {
        if (empty($rulesHandler->getRules())) {
            throw new \InvalidArgumentException('No rules provided!');
        }

        $this->rulesHandler = $rulesHandler;

        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setDescription('Check *.rst files')
            ->addArgument('dir', InputArgument::OPTIONAL, 'Directory', '.')
            ->addOption('rule', 'r', InputOption::VALUE_OPTIONAL, 'Which rule should be applied?')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Dry-Run')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);

        $this->io->title(sprintf('Check *.rst files in: <info>%s</info>', $input->getArgument('dir')));

        $configuredRule = null;
        if (!\is_null($input->getOption('rule'))) {
            $configuredRule = $this->rulesHandler->getRule($input->getOption('rule'));
        }

        if ($input->getOption('dry-run')) {
            $this->dryRun = true;
        }

        $finder = new Finder();
        $finder->files()->name('*.rst')->in($input->getArgument('dir'));

        foreach ($finder as $file) {
            $this->checkFile($file, $configuredRule ?: null);
        }

        if ($this->violations) {
            $this->io->warning('Found invalid files!');
        } else {
            $this->io->success('All files are valid!');
        }

        return $this->violations ? 1 : 0;
    }

    private function checkFile(SplFileInfo $file, ?string $configuredRule = null)
    {
        $this->io->writeln($file->getPathname());

        $lines = new \ArrayIterator(file($file->getRealPath()));

        $violations = [];
        foreach ($lines as $no => $line) {
            if (empty($line)) {
                continue;
            }

            /** @var Rule $rule */
            foreach ($this->rulesHandler->getRules() as $rule) {
                if (!\is_null($configuredRule) && \get_class($rule) !== $configuredRule) {
                    continue;
                }

                $violation = $rule->check($lines, $no);

                if (!empty($violation)) {
                    $violations[] = [
                        $violation,
                        $no + 1,
                        trim($line),
                    ];

                    $this->violations = true;
                }
            }
        }

        if (!empty($violations)) {
            $this->io->table(['Violation', 'Line', 'Extracted line from file'], $violations);
        }
    }
}
