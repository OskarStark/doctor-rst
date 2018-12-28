<?php

namespace App\Rule;

class YamlInsteadOfYmlPrefix implements Rule
{
    public function supportedExtensions(): array
    {
        return ['rst'];
    }

    public function check(string $line)
    {
        if (strstr(strtolower($line), '.. code-block:: yml')) {
            return 'Please use ".. code-block:: yaml" instead of ".. code-block:: yml"';
        }

        if (strstr(strtolower($line), '.yml')) {
            return 'Please use ".yaml" instead of ".yml"';
        }
    }
}