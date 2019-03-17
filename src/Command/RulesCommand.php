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

use App\Annotations\Rule\Description;
use App\Handler\RulesHandler;
use App\Rule\CheckListRule;
use App\Rule\Rule;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RulesCommand extends Command
{
    protected static $defaultName = 'rules';

    /** @var SymfonyStyle */
    private $io;

    /** @var RulesHandler */
    private $rulesHandler;

    /** @var AnnotationReader */
    private $annotationReader;

    public function __construct(RulesHandler $rulesHandler, ?string $name = null)
    {
        $this->rulesHandler = $rulesHandler;

        AnnotationRegistry::loadAnnotationClass(Description::class);

        $this->annotationReader = new AnnotationReader();

        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setDescription('List available rules')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);

        $this->io->writeln('Available rules');
        $this->io->writeln('---------------');
        $this->io->newLine();

        $rules = $this->rulesHandler->getRawRules();

        if (empty($rules)) {
            $this->io->warning('No rules available!');

            return 1;
        }

        foreach ($rules as $rule) {
            $this->rule($rule);
        }
    }

    private function rule(Rule $rule)
    {
        /** @var Description $description */
        $description = $this->annotationReader->getClassAnnotation(new \ReflectionClass(\get_class($rule)), Description::class);

        $this->io->writeln(trim(sprintf(
            '* **%s**%s',
            $rule::getName(),
            !empty($rule::getGroups()) ? sprintf(' [`%s`]', implode('`, `', $rule::getGroups())) : ''
        )));
        $this->io->newLine();

        if (null !== $description) {
            $this->io->writeln(sprintf(
                '  _%s_',
                $description->value
            ));
            $this->io->newLine();
        }

        if ($rule instanceof CheckListRule && !empty($rule::getList())) {
            $this->io->writeln('  Default:');
            foreach ($rule::getList() as $check) {
                $this->io->writeln(sprintf('    - `%s`', $check[0]));
            }
            $this->io->newLine();
        }
    }
}
