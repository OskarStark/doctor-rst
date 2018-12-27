<?php

namespace App\Constraints;

class Typo implements Constraint
{
    public function supportedExtensions(): array
    {
        return ['rst'];
    }

    public function validate(string $line, int $number)
    {
        if (strstr($line, $typo = 'compsoer')) {
            return sprintf('Typo in word "%s"', $typo);
        }
    }
}