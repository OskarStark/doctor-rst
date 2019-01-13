<?php

declare(strict_types=1);

/*
 * This file is part of the rst-checker.
 *
 * (c) Oskar Stark <oskarstark@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Rule\Sonata;

use App\Handler\RulesHandler;
use App\Rule\Rule;
use App\Util\Util;

class PhpCsFixerPhpCodeBlock implements Rule
{
    private $fileDir;

    public function __construct(string $fileDir)
    {
        @mkdir($fileDir);

        $this->fileDir = $fileDir;
    }

    public static function getName(): string
    {
        return 'php_cs_fixer_code_block';
    }

    public static function getGroups(): array
    {
        return [RulesHandler::GROUP_DEV];
    }

    public function check(\ArrayIterator $lines, int $number)
    {
        $lines->seek($number);
        $line = $lines->current();

        if (!Util::codeBlockDirectiveIsTypeOf($line, Util::CODE_BLOCK_PHP)) {
            return;
        }

        $lines->next();

        $phpCode = [];

        while (!\is_null($lines->current()) && ('    ' == substr($lines->current(), 0, 4) || empty(Util::clean($lines->current())))) {
            $phpCode[] = $lines->current();

            $lines->next();
        }

        $tempfile = sprintf(
            '%s/%s.php',
            $this->fileDir,
            uniqid('php_file')
        );

        file_put_contents($tempfile, implode('', $phpCode));

        //var_dump(file_get_contents($tempfile));

//        var_dump(shell_exec(sprintf(
//            'docker run --rm -it -w /app -v %s:/app oskarstark/php-cs-fixer-ga:latest --config=../../',
//            $this->fileDir
//        )));
    }
}
