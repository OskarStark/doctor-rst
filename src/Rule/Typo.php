<?php

namespace App\Rule;

class Typo implements Rule
{
    public function supportedExtensions(): array
    {
        return ['rst'];
    }

    public function check(string $line)
    {
        if (strstr($line, $typo = 'compsoer')) {
            return sprintf('Typo in word "%s"', $typo);
        }
    }
}