<?php

namespace App\Constraints;

class NoConfigYaml implements Constraint
{
    public function supportedExtensions(): array
    {
        return ['rst'];
    }

    public function validate(string $line, int $number)
    {
        if (strstr(strtolower($line), 'app/config/config.yml')) {
            return 'Please use specific config class in "config/packages/..." instead of "app/config/config.yml"';
        }
    }
}