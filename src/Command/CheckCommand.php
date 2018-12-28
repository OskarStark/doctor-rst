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

use App\Rule\Rule;
use PhpParser\Node\Scalar\MagicConst\File;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
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

    /** @var Rule[] */
    private $rules;

    public function __construct(iterable $rules, ?string $name = null)
    {
        /** @var Rule $constraint */
        foreach ($rules as $constraint) {
            foreach ($constraint->supportedExtensions() as $extension) {
                $this->rules[$extension][] = $constraint;
            }
        }

        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setDescription('Check *.rst files')
            ->addArgument('dir', InputArgument::REQUIRED, 'Directory')
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

        if (empty($this->rules[$file->getExtension()])) {
            return;
        }

        $lines = file($file->getRealPath());

        $violations = [];
        foreach ($lines as $no => $line) {
            if (empty($line)) {
                continue;
            }

            foreach ($this->rules[$file->getExtension()] as $rule) {
                $violation = $rule->check($line, $no);

                if (!empty($violation)) {
                    $violations[] = [
                        $violation,
                        $no,
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
