<?php

declare(strict_types=1);

use ComposerUnused\ComposerUnused\Configuration\Configuration;
use ComposerUnused\ComposerUnused\Configuration\NamedFilter;
use ComposerUnused\ComposerUnused\Configuration\PatternFilter;
use Webmozart\Glob\Glob;

return static function (Configuration $config): Configuration {
    return $config
        ->addNamedFilter(NamedFilter::fromString('ext-iconv'))
        ->setAdditionalFilesFor('oskarstark/doctor-rst', [
            __FILE__,
            ...array_merge(
                Glob::glob(__DIR__.'/bin/*.php'),
                Glob::glob(__DIR__.'/config/*.php'),
                Glob::glob(__DIR__.'/public/*.php'),
                Glob::glob(__DIR__.'/templates/*.php'),
            ),
        ]);
};
