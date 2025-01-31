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

namespace App\Tests;

use App\Rst\RstParser;
use Faker\Generator;
use PHPUnit\Framework\TestCase;

abstract class UnitTestCase extends TestCase
{
    final protected static function faker(string $locale = 'de_DE'): Generator
    {
        static $fakers = [];

        if (!\array_key_exists($locale, $fakers)) {
            $fakers[$locale] = new Generator();
        }

        return $fakers[$locale];
    }

    /**
     * @return string[]
     */
    final public static function phpCodeBlocks(): array
    {
        $codeBlocks = [
            RstParser::CODE_BLOCK_PHP,
            RstParser::CODE_BLOCK_PHP_ANNOTATIONS,
            RstParser::CODE_BLOCK_PHP_ATTRIBUTES,
            RstParser::CODE_BLOCK_PHP_SYMFONY,
            RstParser::CODE_BLOCK_PHP_STANDALONE,
        ];

        $result = [];

        foreach ($codeBlocks as $codeBlock) {
            $result[] = '.. code-block:: '.$codeBlock;
        }

        $result[] = 'A PHP code block follows::';

        return $result;
    }
}
