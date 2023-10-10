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

use App\Analyzer\Cache;
use App\Analyzer\InMemoryCache;
use App\Analyzer\MemoizingAnalyzer;
use App\Analyzer\RstAnalyzer;
use App\Command\AnalyzeCommand;
use App\Command\RulesCommand;
use App\Formatter\ConsoleFormatter;
use App\Formatter\GithubFormatter;
use App\Formatter\Registry as FormatterRegistry;
use App\Handler\Registry as HandlerRegistry;
use Composer\Semver\VersionParser;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services->load('App\\', '../src/*');

    $services->alias(Cache::class, InMemoryCache::class);

    $services->set(MemoizingAnalyzer::class)
        ->arg('$analyzer', service(RstAnalyzer::class));

    $services->set(AnalyzeCommand::class)
        ->public();

    $services->set(RulesCommand::class)
        ->public();

    $services->set(FormatterRegistry::class)
        ->args([
            service(ConsoleFormatter::class),
            service(GithubFormatter::class),
        ]);

    $services->load('App\\Rule\\', '../src/Rule')
        ->tag('doctor_rst.rule');

    $services->set(HandlerRegistry::class)
        ->args([tagged_iterator('doctor_rst.rule')]);

    $services->set(VersionParser::class);
};
