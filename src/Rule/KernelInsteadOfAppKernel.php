<?php

namespace App\Rule;

class KernelInsteadOfAppKernel implements Rule
{
    public function supportedExtensions(): array
    {
        return ['rst'];
    }

    public function check(string $line)
    {
        if (strstr($line, 'app/AppKernel.php')) {
            return 'Please use "src/Kernel.php" instead of "app/AppKernel.php"';
        }

        if (strstr($line, 'AppKernel')) {
            return 'Please use "Kernel" instead of "AppKernel"';
        }
    }
}