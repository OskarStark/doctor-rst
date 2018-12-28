<?php

namespace App\Rule;

class NoConfigYaml implements Rule
{
    public function supportedExtensions(): array
    {
        return ['rst'];
    }

    public function check(string $line)
    {
        if (strstr(strtolower($line), 'app/config/config.yml')) {
            return 'Please use specific config class in "config/packages/..." instead of "app/config/config.yml"';
        }
    }
}