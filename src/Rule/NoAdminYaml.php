<?php

declare(strict_types=1);

/*
 * This file is part of the rst-checker.
 *
 * (c) Oskar Stark <oskarstark@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Rule;

class NoAdminYaml implements Rule
{
    public function supportedExtensions(): array
    {
        return ['rst'];
    }

    public function check(string $line)
    {
        if (strstr(strtolower($line), 'admin.yml')) {
            return 'Please use "services.yaml" instead of "admin.yml"';
        }

        if (strstr(strtolower($line), 'admin.yaml')) {
            return 'Please use "services.yaml" instead of "admin.yaml"';
        }
    }
}
