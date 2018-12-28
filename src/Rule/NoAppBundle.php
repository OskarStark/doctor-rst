<?php

namespace App\Rule;

class NoAppBundle implements Rule
{
    public function supportedExtensions(): array
    {
        return ['rst'];
    }

    public function check(string $line)
    {
        if (strstr($line, 'AppBundle')) {
            return 'Please don\'t use "AppBundle" anymore';
        }
    }
}