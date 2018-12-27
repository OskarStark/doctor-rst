<?php

namespace App\Constraints;

class NoPhpPrefix implements  Constraint
{
    public function supportedExtensions(): array
    {
        return ['rst'];
    }

    public function validate(string $line, int $number)
    {
        if (strstr($line, 'php composer')) {
            return 'Please remove "php" prefix';
        }
    }
}