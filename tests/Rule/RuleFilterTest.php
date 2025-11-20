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

namespace App\Tests\Rule;

use App\Rule\FileContentRule;
use App\Rule\FileInfoRule;
use App\Rule\LineContentRule;
use App\Rule\Rule;
use App\Rule\RuleFilter;
use App\Tests\Fixtures\Rule\DummyFileContentRule;
use App\Tests\Fixtures\Rule\DummyFileInfoRule;
use App\Tests\Fixtures\Rule\DummyLineContentRule;
use App\Tests\Fixtures\Rule\DummyRule;
use App\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * @no-named-arguments
 */
final class RuleFilterTest extends UnitTestCase
{
    #[Test]
    public function byTypeReturnsEmptyArrayWhenNoRulesMatch(): void
    {
        $rules = [
            new DummyFileInfoRule(),
            new DummyFileContentRule(),
        ];

        $result = RuleFilter::byType($rules, LineContentRule::class);

        self::assertSame([], $result);
    }

    #[Test]
    public function byTypeReturnsEmptyArrayWhenGivenEmptyArray(): void
    {
        $result = RuleFilter::byType([], FileInfoRule::class);

        self::assertSame([], $result);
    }

    #[Test]
    public function byTypeFiltersFileInfoRules(): void
    {
        $fileInfoRule = new DummyFileInfoRule();
        $fileContentRule = new DummyFileContentRule();
        $lineContentRule = new DummyLineContentRule();

        $rules = [$fileInfoRule, $fileContentRule, $lineContentRule];

        $result = RuleFilter::byType($rules, FileInfoRule::class);

        self::assertCount(1, $result);
        self::assertSame($fileInfoRule, $result[0]);
    }

    #[Test]
    public function byTypeFiltersFileContentRules(): void
    {
        $fileInfoRule = new DummyFileInfoRule();
        $fileContentRule = new DummyFileContentRule();
        $lineContentRule = new DummyLineContentRule();

        $rules = [$fileInfoRule, $fileContentRule, $lineContentRule];

        $result = RuleFilter::byType($rules, FileContentRule::class);

        self::assertCount(1, $result);
        self::assertSame($fileContentRule, $result[1]);
    }

    #[Test]
    public function byTypeFiltersLineContentRules(): void
    {
        $fileInfoRule = new DummyFileInfoRule();
        $fileContentRule = new DummyFileContentRule();
        $lineContentRule = new DummyLineContentRule();

        $rules = [$fileInfoRule, $fileContentRule, $lineContentRule];

        $result = RuleFilter::byType($rules, LineContentRule::class);

        self::assertCount(1, $result);
        self::assertSame($lineContentRule, $result[2]);
    }

    #[Test]
    public function byTypeReturnsMultipleMatchingRules(): void
    {
        $fileInfoRule1 = new DummyFileInfoRule();
        $fileInfoRule2 = new DummyFileInfoRule();
        $fileContentRule = new DummyFileContentRule();

        $rules = [$fileInfoRule1, $fileContentRule, $fileInfoRule2];

        $result = RuleFilter::byType($rules, FileInfoRule::class);

        self::assertCount(2, $result);
        self::assertSame($fileInfoRule1, $result[0]);
        self::assertSame($fileInfoRule2, $result[2]);
    }

    #[Test]
    public function byTypeFiltersBaseRuleInterface(): void
    {
        $fileInfoRule = new DummyFileInfoRule();
        $fileContentRule = new DummyFileContentRule();
        $lineContentRule = new DummyLineContentRule();

        $rules = [$fileInfoRule, $fileContentRule, $lineContentRule];

        $result = RuleFilter::byType($rules, Rule::class);

        self::assertCount(3, $result);
    }

    #[Test]
    public function byTypePreservesArrayKeys(): void
    {
        $fileInfoRule = new DummyFileInfoRule();
        $fileContentRule = new DummyFileContentRule();
        $lineContentRule = new DummyLineContentRule();

        $rules = [
            'rule1' => $fileInfoRule,
            'rule2' => $fileContentRule,
            'rule3' => $lineContentRule,
        ];

        $result = RuleFilter::byType($rules, FileInfoRule::class);

        self::assertArrayHasKey('rule1', $result);
        self::assertSame($fileInfoRule, $result['rule1']);
    }
}
