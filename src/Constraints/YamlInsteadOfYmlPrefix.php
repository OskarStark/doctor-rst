<?php

namespace App\Constraints;

class YamlInsteadOfYmlPrefix implements Constraint
{
    public function supportedExtensions(): array
    {
        return ['rst'];
    }

    public function validate(string $line, int $number)
    {
        if (strstr(strtolower($line), '.yml')) {
            return 'Please use ".yaml" instead of ".yml"';
        }

        if (strstr(strtolower($line), '.. code-block:: yml')) {
            return 'Please use ".. code-block:: yaml" instead of ".. code-block:: yml"';
        }
    }
}