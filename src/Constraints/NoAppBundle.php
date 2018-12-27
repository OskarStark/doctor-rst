<?php

namespace App\Constraints;

class NoAppBundle implements Constraint
{
    public function supportedExtensions(): array
    {
        return ['rst'];
    }

    public function validate(string $line, int $number)
    {
        if (strstr($line, 'AppBundle')) {
            return 'Please don\'t use "AppBundle" anymore';
        }
    }
}