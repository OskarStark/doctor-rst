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

namespace App\Tests\Value;

use App\Tests\Fixtures\Rule\DummyRule;
use App\Tests\UnitTestCase;
use App\Value\RulesConfiguration;
use PHPUnit\Framework\Attributes\Test;

final class RulesConfigurationTest extends UnitTestCase
{
    #[Test]
    public function rulesForAll(): void
    {
        $rulesConfiguration = new RulesConfiguration();
        self::assertFalse($rulesConfiguration->hasRulesForAll());

        $firstRule = new DummyRule();
        $rulesConfiguration->addRuleForAll($firstRule);
        self::assertTrue($rulesConfiguration->hasRulesForAll());

        self::assertSame(
            [
                $firstRule,
            ],
            $rulesConfiguration->getRulesForFilePath('foobar'),
        );

        $secondRule = new DummyRule();
        $rulesConfiguration->addRuleForAll($secondRule);

        self::assertSame(
            [
                $firstRule,
                $secondRule,
            ],
            $rulesConfiguration->getRulesForFilePath('foobar'),
        );

        $rulesConfiguration->setRulesForAll([$secondRule]);

        self::assertSame(
            [
                $secondRule,
            ],
            $rulesConfiguration->getRulesForFilePath('foobar'),
        );
    }

    #[Test]
    public function excludedRulesForFile(): void
    {
        $rulesConfiguration = new RulesConfiguration();

        $firstRule = new DummyRule();
        $secondRule = new DummyRule();
        $notRegisteredRule = new DummyRule();

        $rulesConfiguration->setRulesForAll([$firstRule, $secondRule]);

        $rulesConfiguration->excludeRulesForFilePath('foo', [$firstRule, $notRegisteredRule]);

        self::assertSame(
            [
                1 => $secondRule,
            ],
            $rulesConfiguration->getRulesForFilePath('foo'),
        );

        self::assertSame(
            [
                0 => $firstRule,
                1 => $secondRule,
            ],
            $rulesConfiguration->getRulesForFilePath('bar'),
        );
    }
}
