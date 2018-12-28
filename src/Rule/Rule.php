<?php

namespace App\Rule;

interface Rule
{
    public function supportedExtensions(): array;

    public function check(string $line);
}