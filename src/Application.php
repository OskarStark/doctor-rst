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

namespace App;

use App\Command\AnalyseCommand;
use App\Command\RulesCommand;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class Application extends BaseApplication
{
    public const VERSION = '@git-version@';

    public function __construct()
    {
        parent::__construct('DOCtor-RST', self::VERSION);
    }

    public function doRun(InputInterface $input, OutputInterface $output): int
    {
        $container = $this->buildContainer();

        /** @var AnalyseCommand $analyseCommand */
        $analyseCommand = $container->get(AnalyseCommand::class);

        /** @var RulesCommand $rulesCommand */
        $rulesCommand = $container->get(RulesCommand::class);

        $this->addCommands([$analyseCommand, $rulesCommand]);

        return parent::doRun($input, $output);
    }

    private function buildContainer(): ContainerBuilder
    {
        $container = new ContainerBuilder();

        $fileLoader = new YamlFileLoader($container, new FileLocator(\dirname(__DIR__).'/config/'));
        $fileLoader->load('services.yaml');

        $container->compile();

        return $container;
    }
}
