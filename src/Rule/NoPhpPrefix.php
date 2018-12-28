<?php

namespace App\Rule;

class NoPhpPrefix implements  Rule
{
    public function supportedExtensions(): array
    {
        return ['rst'];
    }

    public function check(string $line)
    {
        if (strstr($line, 'php composer')) {
            return 'Please remove "php" prefix';
        }
    }
}