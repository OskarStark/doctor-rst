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

namespace App\Tests\Fixtures\Rule;

use App\Rule\AbstractRule;
use App\Rule\FileInfoRule;
use App\Value\NullViolation;
use App\Value\ViolationInterface;

final class DummyFileInfoRule extends AbstractRule implements FileInfoRule
{
    public function check(\SplFileInfo $file): ViolationInterface
    {
        return NullViolation::create();
    }
}
