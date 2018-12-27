<?php

namespace App\Constraints;

class NoComposerPharPrefix implements Constraint
{
    public function supportedExtensions(): array
    {
        return ['rst'];
    }

    public function validate(string $line, int $number)
    {
        if (strstr($line, 'composer.phar')) {
            return 'Please use "composer" instead of "composer.phar"';
        }
    }
}