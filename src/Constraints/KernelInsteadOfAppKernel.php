<?php

namespace App\Constraints;

class KernelInsteadOfAppKernel implements Constraint
{
    public function supportedExtensions(): array
    {
        return ['rst'];
    }

    public function validate(string $line, int $number)
    {
        if (strstr($line, 'app/AppKernel.php')) {
            return 'Please use "src/Kernel.php" instead of "app/AppKernel.php"';
        }

        if (strstr($line, 'AppKernel')) {
            return 'Please use "Kernel" instead of "AppKernel"';
        }
    }
}