<?php

declare(strict_types=1);

/*
 * This file is part of DOCtor-RST.
 *
 * (c) Oskar Stark <oskarstark@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\Rst;

use App\Rst\RstParser;
use App\Rule\CorrectCodeBlockDirectiveBasedOnTheContent;
use App\Rule\Rule;
use App\Value\Lines;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Service\ResetInterface;

final class RulesAgainstRealFilesTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider fileProvider
     *
     * @group files
     */
    public function fileAgainstRule(Rule $rule, string $filepath)
    {
        $content = file($filepath);

        if (false == $content) {
            self::fail(sprintf('Cannot parse file: %s', $filepath));
        }

        $lines = Lines::fromArray($content);

        $violations = [];
        foreach ($lines->toIterator() as $no => $line) {
            \assert(\is_int($no));

            if (!$rule::runOnlyOnBlankline() && RstParser::isBlankLine($line)) {
                continue;
            }

            if (Rule::TYPE_FILE === $rule::getType() && $no > 0) {
                continue;
            }
            $violation = $rule->check($lines, $no);

            if (null !== $violation) {
                $violations[] = [
                    $rule::getName()->asString(),
                    $violation,
                    $no + 1,
                    Rule::TYPE_FILE === $rule::getType() ? '' : trim($line),
                ];
            }

            if ($rule instanceof ResetInterface) {
                $rule->reset();
            }
        }

        self::assertSame([], $violations);
    }

    /**
     * @return \Generator<array{0: Rule, 1: string}>
     */
    public function fileProvider(): \Generator
    {
        yield [
            new CorrectCodeBlockDirectiveBasedOnTheContent(),
            __DIR__.'/Fixtures/EasyAdminBundle/vichuploaderbundle.rst',
        ];
    }
}
