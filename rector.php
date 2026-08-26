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

use Rector\CodeQuality\Rector\Attribute\SortAttributeNamedArgsRector;
use Rector\CodeQuality\Rector\ClassMethod\LocallyCalledStaticMethodToNonStaticRector;
use Rector\CodeQuality\Rector\FuncCall\SortCallLikeNamedArgsRector;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\Cast\RecastingRemovalRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUnusedPublicMethodParameterRector;
use Rector\DeadCode\Rector\MethodCall\RemoveNullArgOnNullDefaultParamRector;
use Rector\PHPUnit\CodeQuality\Rector\Class_\PreferPHPUnitSelfCallRector;
use Rector\PHPUnit\CodeQuality\Rector\Class_\PreferPHPUnitThisCallRector;

return RectorConfig::configure()
    ->withParallel()
    ->withPaths([
        __DIR__.'/composer-unused.php',
        __DIR__.'/.php-cs-fixer.dist.php',
        __DIR__.'/rector.php',
        __DIR__.'/src',
        __DIR__.'/tests',
    ])
    ->withImportNames(importShortClasses: false)
    ->withPHPStanConfigs([
        getcwd().'/phpstan.neon.dist',
        'vendor/phpstan/phpstan-phpunit/extension.neon',
        'vendor/phpstan/phpstan-webmozart-assert/extension.neon',
    ])
    ->withPhpSets()
    ->withComposerBased(
        phpunit: true,
        symfony: true,
    )
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        earlyReturn: true,
        phpunitCodeQuality: true,
        symfonyCodeQuality: true,
        symfonyConfigs: true,
    )
    ->withSkip([
        SortAttributeNamedArgsRector::class,
        SortCallLikeNamedArgsRector::class,
        RemoveUnusedPublicMethodParameterRector::class => [
            __DIR__.'/src/EventListener', // Keep event args in listeners for consistency
        ],
        RemoveNullArgOnNullDefaultParamRector::class => [
            __DIR__.'/tests', // Keep explicit null arguments in tests for clarity
        ],
        LocallyCalledStaticMethodToNonStaticRector::class,
        PreferPHPUnitThisCallRector::class,
        RecastingRemovalRector::class, // Keep explicit casts for clarity
    ])
    ->withRules([
        PreferPHPUnitSelfCallRector::class,
    ]);
