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

use ComposerUnused\ComposerUnused\Configuration\Configuration;
use ComposerUnused\ComposerUnused\Configuration\NamedFilter;
use Webmozart\Glob\Glob;

return static fn (Configuration $config): Configuration => $config
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
