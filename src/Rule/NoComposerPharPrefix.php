<?php

namespace App\Rule;

class NoComposerPharPrefix implements Rule
{
    public function supportedExtensions(): array
    {
        return ['rst'];
    }

    public function check(string $line)
    {
        if (strstr($line, 'composer.phar')) {
            return 'Please use "composer" instead of "composer.phar"';
        }
    }
}