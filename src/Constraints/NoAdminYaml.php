<?php

namespace App\Constraints;

class NoAdminYaml implements Constraint
{
    public function supportedExtensions(): array
    {
        return ['rst'];
    }

    public function validate(string $line, int $number)
    {
        if (strstr(strtolower($line), 'admin.yml')) {
            return 'Please use "services.yaml" instead of "admin.yml"';
        }

        if (strstr(strtolower($line), 'admin.yaml')) {
            return 'Please use "services.yaml" instead of "admin.yaml"';
        }
    }
}