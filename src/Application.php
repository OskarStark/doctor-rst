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

namespace App;

use App\Command\AnalyzeCommand;
use App\Command\RulesCommand;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

class Application extends BaseApplication
{
    final public const VERSION = '@git-version@';

    public function __construct(private AnalyzeCommand $analyzeCommand, private RulesCommand $rulesCommand)
    {
        parent::__construct('DOCtor-RST', self::VERSION);

        $this->getDefinition()->addOptions([
            new InputOption('--no-cache', null, InputOption::VALUE_NONE, 'Disable caching mechanisms'),
            new InputOption('--cache-file', null, InputOption::VALUE_REQUIRED, 'Path to the cache file', '.doctor-rst.cache'),
        ]);
    }

    public function doRun(InputInterface $input, OutputInterface $output): int
    {
        $container = $this->buildContainer($input);

        /** @var AnalyzeCommand $analyzeCommand */
        $analyzeCommand = $this->analyzeCommand;

        /** @var RulesCommand $rulesCommand */
        $rulesCommand = $this->rulesCommand;

        $this->addCommands([$analyzeCommand, $rulesCommand]);

        return parent::doRun($input, $output);
    }

    private function buildContainer(InputInterface $input): ContainerBuilder
    {
        $container = new ContainerBuilder();

        $fileLoader = new PhpFileLoader($container, new FileLocator(\dirname(__DIR__).'/config/'));
        $fileLoader->load('services.php');

        if (false === $input->hasParameterOption('--no-cache')) {
            $container->setParameter(
                'cache.file',
                $input->getParameterOption('--cache-file', getcwd().'/.doctor-rst.cache'),
            );

            $fileLoader->load('cache.php');
        }

        $container->compile(true);

        return $container;
    }
}
