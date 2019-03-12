<?php

namespace App\Helper;

use App\Rst\RstParser;

final class PhpHelper
{
    public static function isComment(string $line): bool
    {
        if (preg_match('/^(#|\/\/)(.*)/', RstParser::clean($line))) {
            return true;
        }
        return false;
    }
}