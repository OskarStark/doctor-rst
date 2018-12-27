<?php

namespace App\Command;

use App\Constraints\Constraint;
use PhpParser\Node\Scalar\MagicConst\File;
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

    /** @var Constraint[] */
    private $constraints;

    public function __construct(iterable $constraints, ?string $name = null)
    {
        /** @var Constraint $constraint */
        foreach ($constraints as $constraint) {
            foreach ($constraint->supportedExtensions() as $extension) {
                $this->constraints[$extension][] = $constraint;
            }
        }

        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setDescription('Check *.rst files')
            ->addArgument('dir', InputArgument::REQUIRED, 'Directory')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);

        $this->io->title(sprintf('Check *.rst files in: <info>%s</info>', $input->getArgument('dir')));

        $finder = new Finder();
        $finder->files()->name('*.rst')->in($input->getArgument('dir'));

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

        if (empty($this->constraints[$file->getExtension()])) {
            return;
        }

        $lines = file($file->getRealPath());

        $violations = [];
        foreach ($lines as $no => $line) {
            if (empty($line)) {
                continue;
            }

            foreach ($this->constraints[$file->getExtension()] as $constraint) {
                $violation = $constraint->validate($line, $no);

                if (!empty($violation)) {
                    $violations[] = [
                        $violation,
                        $no,
                        trim($line)
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
