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

use Rector\CodingStyle\Rector\FuncCall\ArraySpreadInsteadOfArrayMergeRector;
use Rector\Config\RectorConfig;
use Rector\Doctrine\Set\DoctrineSetList;
use Rector\Php83\Rector\ClassMethod\AddOverrideAttributeToOverriddenMethodsRector;
use Rector\PHPUnit\CodeQuality\Rector\Class_\AddSeeTestAnnotationRector;
use Rector\PHPUnit\CodeQuality\Rector\Class_\PreferPHPUnitThisCallRector;
use Rector\PHPUnit\CodeQuality\Rector\ClassMethod\ReplaceTestAnnotationWithPrefixedFunctionRector;
use Rector\PHPUnit\PHPUnit100\Rector\Class_\StaticDataProviderClassMethodRector;
use Rector\PHPUnit\Rector\Class_\PreferPHPUnitSelfCallRector;
use Rector\PHPUnit\Set\PHPUnitLevelSetList;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Set\ValueObject\SetList;
use Rector\Symfony\Set\SymfonySetList;
use Rector\ValueObject\PhpVersion;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->parallel();
    $rectorConfig->paths([
        __DIR__.'/composer-unused.php',
        __DIR__.'/.php-cs-fixer.dist.php',
        __DIR__.'/rector.php',
        __DIR__.'/src',
        __DIR__.'/tests',
    ]);

    $rectorConfig->phpVersion(PhpVersion::PHP_82);
    $rectorConfig->importNames();
    $rectorConfig->importShortClasses(false);
    $rectorConfig->phpstanConfigs([
        getcwd().'/phpstan.neon.dist',
        'vendor/phpstan/phpstan-phpunit/extension.neon',
        'vendor/phpstan/phpstan-webmozart-assert/extension.neon',
    ]);

    $rectorConfig->sets([
        SetList::PHP_82,
        PHPUnitLevelSetList::UP_TO_PHPUNIT_90,
        PHPUnitSetList::PHPUNIT_CODE_QUALITY,
        SymfonySetList::SYMFONY_64,
        SymfonySetList::SYMFONY_CODE_QUALITY,
        DoctrineSetList::DOCTRINE_CODE_QUALITY,
    ]);

    $rectorConfig->skip([
        AddOverrideAttributeToOverriddenMethodsRector::class,
        ArraySpreadInsteadOfArrayMergeRector::class,
        PreferPHPUnitThisCallRector::class,
        ReplaceTestAnnotationWithPrefixedFunctionRector::class,
        AddSeeTestAnnotationRector::class,
    ]);

    $rectorConfig->rule(PreferPHPUnitSelfCallRector::class);

    /**
     * @see https://github.com/rectorphp/rector-phpunit/blob/main/docs/rector_rules_overview.md#staticdataproviderclassmethodrector
     */
    $rectorConfig->rule(StaticDataProviderClassMethodRector::class);
};
