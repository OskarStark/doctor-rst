<?php

namespace App\Rule;

class NoAdminYaml implements Rule
{
    public function supportedExtensions(): array
    {
        return ['rst'];
    }

    public function check(string $line)
    {
        if (strstr(strtolower($line), 'admin.yml')) {
            return 'Please use "services.yaml" instead of "admin.yml"';
        }

        if (strstr(strtolower($line), 'admin.yaml')) {
            return 'Please use "services.yaml" instead of "admin.yaml"';
        }
    }
}