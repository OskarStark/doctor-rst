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
use Rector\CodeQuality\Rector\FuncCall\SimplifyRegexPatternRector;
use Rector\CodeQuality\Rector\FuncCall\SortCallLikeNamedArgsRector;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\Cast\RecastingRemovalRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUnusedPublicMethodParameterRector;
use Rector\DeadCode\Rector\MethodCall\RemoveNullArgOnNullDefaultParamRector;
use Rector\EarlyReturn\Rector\If_\ChangeOrIfContinueToMultiContinueRector;
use Rector\EarlyReturn\Rector\Return_\ReturnBinaryOrToEarlyReturnRector;
use Rector\Php81\Rector\FuncCall\NullToStrictStringFuncCallArgRector;
use Rector\Php83\Rector\ClassMethod\AddOverrideAttributeToOverriddenMethodsRector;
use Rector\PHPUnit\CodeQuality\Rector\Class_\PreferPHPUnitSelfCallRector;
use Rector\PHPUnit\CodeQuality\Rector\Class_\PreferPHPUnitThisCallRector;
use Rector\PHPUnit\PHPUnit100\Rector\Class_\StaticDataProviderClassMethodRector;
use Rector\Strict\Rector\Empty_\DisallowedEmptyRuleFixerRector;
use Rector\Symfony\CodeQuality\Rector\Class_\ControllerMethodInjectionToConstructorRector;

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
        ControllerMethodInjectionToConstructorRector::class, // Route parameters should stay as action parameters, not constructor
        SimplifyRegexPatternRector::class, // Keep explicit regex patterns for better readability
        RemoveUnusedPublicMethodParameterRector::class => [
            __DIR__.'/src/EventListener', // Keep event args in listeners for consistency
        ],
        RemoveNullArgOnNullDefaultParamRector::class => [
            __DIR__.'/tests', // Keep explicit null arguments in tests for clarity
        ],
        AddOverrideAttributeToOverriddenMethodsRector::class,
        LocallyCalledStaticMethodToNonStaticRector::class,
        PreferPHPUnitThisCallRector::class,
        NullToStrictStringFuncCallArgRector::class, // Avoid redundant casts when value is already a string
        ReturnBinaryOrToEarlyReturnRector::class, // Keep combined conditions readable instead of splitting into multiple returns
        ChangeOrIfContinueToMultiContinueRector::class, // Keep combined conditions with continue readable
        RecastingRemovalRector::class, // Keep explicit casts for clarity
        DisallowedEmptyRuleFixerRector::class, // Keep empty() for readability
    ])
    ->withRules([
        PreferPHPUnitSelfCallRector::class,
        StaticDataProviderClassMethodRector::class,
    ]);
