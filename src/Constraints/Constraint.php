<?php

namespace App\Constraints;

interface Constraint
{
    public function supportedExtensions(): array;

    public function validate(string $line, int $number);
}