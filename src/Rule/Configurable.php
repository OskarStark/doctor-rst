<?php

declare(strict_types=1);

/**
 * This file is part of DOCtor-RST.
 *
 * (c) Oskar Stark <oskarstark@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Rule;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @no-named-arguments
 */
interface Configurable
{
    public function configureOptions(OptionsResolver $resolver): OptionsResolver;

    public function setOptions(array $options): void;
}
