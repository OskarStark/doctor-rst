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
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ListRulesCommand extends Command
{
    protected static $defaultName = 'list:rules';

    /** @var SymfonyStyle */
    private $io;

    /** @var RulesHandler */
    private $rulesHandler;

    public function __construct(RulesHandler $rulesHandler, ?string $name = null)
    {
        $this->rulesHandler = $rulesHandler;

        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setDescription('List available rules')
            ->addOption('group', 'g', InputOption::VALUE_REQUIRED, 'Rules in group')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);

        $this->io->title(trim(sprintf(
            'List available rules %s',
            (string) $input->getOption('group')
                ? sprintf('for group: <info>%s</info>', (string) $input->getOption('group'))
                : ''
        )));

        if (!empty($input->getOption('group'))) {
            $rules = $this->rulesHandler->getRulesByGroup((string) $input->getOption('group'));
        } else {
            $rules = $this->rulesHandler->getRules();
        }

        if (empty($rules)) {
            $this->io->warning('No rules available!');

            return 1;
        }

        dump($rules);
    }
}
