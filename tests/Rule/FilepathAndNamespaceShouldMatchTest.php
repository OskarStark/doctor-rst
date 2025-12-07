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

use App\Rule\FilepathAndNamespaceShouldMatch;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

/**
 * @no-named-arguments
 */
final class FilepathAndNamespaceShouldMatchTest extends UnitTestCase
{
    private const array DEFAULT_PREFIXES = [
        'src/',
        'lib/',
        'app/',
        'tests/',
        'bundle/',
    ];

    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, array $prefixes, RstSample $sample): void
    {
        $rule = new FilepathAndNamespaceShouldMatch();
        $rule->setOptions(['prefixes' => $prefixes]);

        self::assertEquals(
            $expected,
            $rule->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    public static function checkProvider(): iterable
    {
        $codeBlocks = self::phpCodeBlocks();

        // VALID - filepath and namespace match
        foreach ($codeBlocks as $codeBlock) {
            yield 'valid: matching filepath and namespace - '.$codeBlock => [
                NullViolation::create(),
                self::DEFAULT_PREFIXES,
                new RstSample([
                    $codeBlock,
                    '',
                    '    // src/Acme/FooBundle/Entity/User.php',
                    '',
                    '    namespace Acme\FooBundle\Entity;',
                ]),
            ];

            yield 'valid: matching filepath and namespace without blank lines - '.$codeBlock => [
                NullViolation::create(),
                self::DEFAULT_PREFIXES,
                new RstSample([
                    $codeBlock,
                    '    // src/Acme/FooBundle/Entity/User.php',
                    '    namespace Acme\FooBundle\Entity;',
                ]),
            ];

            yield 'valid: matching filepath with lib prefix - '.$codeBlock => [
                NullViolation::create(),
                self::DEFAULT_PREFIXES,
                new RstSample([
                    $codeBlock,
                    '',
                    '    // lib/Acme/FooBundle/Entity/User.php',
                    '    namespace Acme\FooBundle\Entity;',
                ]),
            ];

            yield 'valid: matching filepath with app prefix - '.$codeBlock => [
                NullViolation::create(),
                self::DEFAULT_PREFIXES,
                new RstSample([
                    $codeBlock,
                    '',
                    '    // app/Entity/User.php',
                    '    namespace Entity;',
                ]),
            ];

            yield 'valid: matching filepath with tests prefix - '.$codeBlock => [
                NullViolation::create(),
                self::DEFAULT_PREFIXES,
                new RstSample([
                    $codeBlock,
                    '',
                    '    // tests/Functional/UserTest.php',
                    '    namespace Functional;',
                ]),
            ];

            // Only filepath, no namespace - should not report
            yield 'valid: only filepath, no namespace - '.$codeBlock => [
                NullViolation::create(),
                self::DEFAULT_PREFIXES,
                new RstSample([
                    $codeBlock,
                    '',
                    '    // src/Acme/FooBundle/Entity/User.php',
                    '',
                    '    class User {}',
                ]),
            ];

            // Only namespace, no filepath - should not report
            yield 'valid: only namespace, no filepath - '.$codeBlock => [
                NullViolation::create(),
                self::DEFAULT_PREFIXES,
                new RstSample([
                    $codeBlock,
                    '',
                    '    namespace Acme\FooBundle\Entity;',
                    '',
                    '    class User {}',
                ]),
            ];

            // No filepath and no namespace - should not report
            yield 'valid: no filepath and no namespace - '.$codeBlock => [
                NullViolation::create(),
                self::DEFAULT_PREFIXES,
                new RstSample([
                    $codeBlock,
                    '',
                    '    class User {}',
                ]),
            ];

            // File in root src directory (no subdirectory) - should not report
            yield 'valid: file in root src directory - '.$codeBlock => [
                NullViolation::create(),
                self::DEFAULT_PREFIXES,
                new RstSample([
                    $codeBlock,
                    '',
                    '    // src/Kernel.php',
                    '    class Kernel {}',
                ]),
            ];
        }

        // INVALID - filepath and namespace do not match
        foreach ($codeBlocks as $codeBlock) {
            yield 'invalid: namespace does not match filepath - '.$codeBlock => [
                Violation::from(
                    'The namespace "Acme\WrongBundle\Entity" does not match the filepath "src/Acme/FooBundle/Entity/User.php", expected namespace "Acme\FooBundle\Entity"',
                    'filename',
                    5,
                    'namespace Acme\WrongBundle\Entity;',
                ),
                self::DEFAULT_PREFIXES,
                new RstSample([
                    $codeBlock,
                    '',
                    '    // src/Acme/FooBundle/Entity/User.php',
                    '',
                    '    namespace Acme\WrongBundle\Entity;',
                ]),
            ];

            yield 'invalid: namespace does not match filepath without blank lines - '.$codeBlock => [
                Violation::from(
                    'The namespace "Wrong\Namespace" does not match the filepath "src/Acme/FooBundle/Entity/User.php", expected namespace "Acme\FooBundle\Entity"',
                    'filename',
                    3,
                    'namespace Wrong\Namespace;',
                ),
                self::DEFAULT_PREFIXES,
                new RstSample([
                    $codeBlock,
                    '    // src/Acme/FooBundle/Entity/User.php',
                    '    namespace Wrong\Namespace;',
                ]),
            ];

            yield 'invalid: namespace partially matches but is wrong - '.$codeBlock => [
                Violation::from(
                    'The namespace "Acme\FooBundle" does not match the filepath "src/Acme/FooBundle/Entity/User.php", expected namespace "Acme\FooBundle\Entity"',
                    'filename',
                    3,
                    'namespace Acme\FooBundle;',
                ),
                self::DEFAULT_PREFIXES,
                new RstSample([
                    $codeBlock,
                    '    // src/Acme/FooBundle/Entity/User.php',
                    '    namespace Acme\FooBundle;',
                ]),
            ];
        }

        // Test with custom prefix
        yield 'valid: custom prefix configuration' => [
            NullViolation::create(),
            ['custom/'],
            new RstSample([
                '.. code-block:: php',
                '',
                '    // custom/Acme/FooBundle/Entity/User.php',
                '    namespace Acme\FooBundle\Entity;',
            ]),
        ];

        yield 'invalid: prefix not configured' => [
            Violation::from(
                'The namespace "Acme\FooBundle\Entity" does not match the filepath "src/Acme/FooBundle/Entity/User.php", expected namespace "src\Acme\FooBundle\Entity"',
                'filename',
                4,
                'namespace Acme\FooBundle\Entity;',
            ),
            ['custom/'],
            new RstSample([
                '.. code-block:: php',
                '',
                '    // src/Acme/FooBundle/Entity/User.php',
                '    namespace Acme\FooBundle\Entity;',
            ]),
        ];
    }
}
