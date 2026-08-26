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

final class FilepathAndNamespaceShouldMatchTest extends UnitTestCase
{
    /**
     * Default namespace mappings: filepath prefix => namespace prefix.
     *
     * @var array<string, string>
     */
    private const array DEFAULT_NAMESPACE_MAPPING = [
        'src/' => '',
        'lib/' => '',
        'app/' => '',
        'tests/' => '',
        'bundle/' => '',
    ];

    /**
     * @param array<string, list<string>|string> $namespaceMapping
     */
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, array $namespaceMapping, RstSample $sample): void
    {
        $rule = new FilepathAndNamespaceShouldMatch();
        $rule->setOptions(['namespace_mapping' => $namespaceMapping]);

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
                self::DEFAULT_NAMESPACE_MAPPING,
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
                self::DEFAULT_NAMESPACE_MAPPING,
                new RstSample([
                    $codeBlock,
                    '    // src/Acme/FooBundle/Entity/User.php',
                    '    namespace Acme\FooBundle\Entity;',
                ]),
            ];

            yield 'valid: matching filepath with lib prefix - '.$codeBlock => [
                NullViolation::create(),
                self::DEFAULT_NAMESPACE_MAPPING,
                new RstSample([
                    $codeBlock,
                    '',
                    '    // lib/Acme/FooBundle/Entity/User.php',
                    '    namespace Acme\FooBundle\Entity;',
                ]),
            ];

            yield 'valid: matching filepath with app prefix - '.$codeBlock => [
                NullViolation::create(),
                self::DEFAULT_NAMESPACE_MAPPING,
                new RstSample([
                    $codeBlock,
                    '',
                    '    // app/Entity/User.php',
                    '    namespace Entity;',
                ]),
            ];

            yield 'valid: matching filepath with tests prefix - '.$codeBlock => [
                NullViolation::create(),
                self::DEFAULT_NAMESPACE_MAPPING,
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
                self::DEFAULT_NAMESPACE_MAPPING,
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
                self::DEFAULT_NAMESPACE_MAPPING,
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
                self::DEFAULT_NAMESPACE_MAPPING,
                new RstSample([
                    $codeBlock,
                    '',
                    '    class User {}',
                ]),
            ];

            // File in root src directory (no subdirectory) - should not report
            yield 'valid: file in root src directory - '.$codeBlock => [
                NullViolation::create(),
                self::DEFAULT_NAMESPACE_MAPPING,
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
                self::DEFAULT_NAMESPACE_MAPPING,
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
                self::DEFAULT_NAMESPACE_MAPPING,
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
                self::DEFAULT_NAMESPACE_MAPPING,
                new RstSample([
                    $codeBlock,
                    '    // src/Acme/FooBundle/Entity/User.php',
                    '    namespace Acme\FooBundle;',
                ]),
            ];
        }

        // Test with custom prefix (no namespace prefix)
        yield 'valid: custom prefix configuration' => [
            NullViolation::create(),
            ['custom/' => ''],
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
            ['custom/' => ''],
            new RstSample([
                '.. code-block:: php',
                '',
                '    // src/Acme/FooBundle/Entity/User.php',
                '    namespace Acme\FooBundle\Entity;',
            ]),
        ];

        // Test with namespace mapping (src/ -> App\)
        yield 'valid: src maps to App namespace' => [
            NullViolation::create(),
            ['src/' => 'App\\'],
            new RstSample([
                '.. code-block:: php',
                '',
                '    // src/Form/DataTransformer/IssueToNumberTransformer.php',
                '    namespace App\Form\DataTransformer;',
            ]),
        ];

        yield 'invalid: src maps to App namespace but namespace is wrong' => [
            Violation::from(
                'The namespace "Form\DataTransformer" does not match the filepath "src/Form/DataTransformer/IssueToNumberTransformer.php", expected namespace "App\Form\DataTransformer"',
                'filename',
                4,
                'namespace Form\DataTransformer;',
            ),
            ['src/' => 'App\\'],
            new RstSample([
                '.. code-block:: php',
                '',
                '    // src/Form/DataTransformer/IssueToNumberTransformer.php',
                '    namespace Form\DataTransformer;',
            ]),
        ];

        yield 'valid: tests maps to App\Tests namespace' => [
            NullViolation::create(),
            ['src/' => 'App\\', 'tests/' => 'App\\Tests\\'],
            new RstSample([
                '.. code-block:: php',
                '',
                '    // tests/Functional/UserTest.php',
                '    namespace App\Tests\Functional;',
            ]),
        ];

        yield 'valid: file in root src directory with App namespace' => [
            NullViolation::create(),
            ['src/' => 'App\\'],
            new RstSample([
                '.. code-block:: php',
                '',
                '    // src/Kernel.php',
                '    namespace App;',
            ]),
        ];

        // Test with array of namespace prefixes
        yield 'valid: src maps to multiple namespaces - App matches' => [
            NullViolation::create(),
            ['src/' => ['App\\', 'Acme\\']],
            new RstSample([
                '.. code-block:: php',
                '',
                '    // src/Form/DataTransformer/IssueToNumberTransformer.php',
                '    namespace App\Form\DataTransformer;',
            ]),
        ];

        yield 'valid: src maps to multiple namespaces - Acme matches' => [
            NullViolation::create(),
            ['src/' => ['App\\', 'Acme\\']],
            new RstSample([
                '.. code-block:: php',
                '',
                '    // src/Form/DataTransformer/IssueToNumberTransformer.php',
                '    namespace Acme\Form\DataTransformer;',
            ]),
        ];

        yield 'invalid: src maps to multiple namespaces but namespace is wrong' => [
            Violation::from(
                'The namespace "Wrong\Form\DataTransformer" does not match the filepath "src/Form/DataTransformer/IssueToNumberTransformer.php", expected namespace "App\Form\DataTransformer" or "Acme\Form\DataTransformer"',
                'filename',
                4,
                'namespace Wrong\Form\DataTransformer;',
            ),
            ['src/' => ['App\\', 'Acme\\']],
            new RstSample([
                '.. code-block:: php',
                '',
                '    // src/Form/DataTransformer/IssueToNumberTransformer.php',
                '    namespace Wrong\Form\DataTransformer;',
            ]),
        ];

        yield 'valid: file in root src directory with multiple namespace options - App matches' => [
            NullViolation::create(),
            ['src/' => ['App\\', 'Acme\\']],
            new RstSample([
                '.. code-block:: php',
                '',
                '    // src/Kernel.php',
                '    namespace App;',
            ]),
        ];

        yield 'valid: file in root src directory with multiple namespace options - Acme matches' => [
            NullViolation::create(),
            ['src/' => ['App\\', 'Acme\\']],
            new RstSample([
                '.. code-block:: php',
                '',
                '    // src/Kernel.php',
                '    namespace Acme;',
            ]),
        ];
    }

    /**
     * @param array<string, list<string>|string> $namespaceMapping
     * @param array<int, string>                 $ignoredPaths
     */
    #[Test]
    #[DataProvider('checkWithIgnoredPathsProvider')]
    public function checkWithIgnoredPaths(ViolationInterface $expected, array $namespaceMapping, array $ignoredPaths, RstSample $sample): void
    {
        $rule = new FilepathAndNamespaceShouldMatch();
        $rule->setOptions([
            'namespace_mapping' => $namespaceMapping,
            'ignored_paths' => $ignoredPaths,
        ]);

        self::assertEquals(
            $expected,
            $rule->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    public static function checkWithIgnoredPathsProvider(): iterable
    {
        yield 'valid: config/ path is ignored with regex' => [
            NullViolation::create(),
            self::DEFAULT_NAMESPACE_MAPPING,
            ['/^config\//'],
            new RstSample([
                '.. code-block:: php',
                '',
                '    // config/services.php',
                '    namespace Symfony\Component\DependencyInjection\Loader\Configurator;',
            ]),
        ];

        yield 'valid: Config/ path is ignored (case insensitive regex)' => [
            NullViolation::create(),
            self::DEFAULT_NAMESPACE_MAPPING,
            ['/^config\//i'],
            new RstSample([
                '.. code-block:: php',
                '',
                '    // Config/services.php',
                '    namespace Symfony\Component\DependencyInjection\Loader\Configurator;',
            ]),
        ];

        yield 'valid: multiple ignored paths with regex' => [
            NullViolation::create(),
            self::DEFAULT_NAMESPACE_MAPPING,
            ['/^config\//', '/^migrations\//'],
            new RstSample([
                '.. code-block:: php',
                '',
                '    // migrations/Version20200101.php',
                '    namespace DoctrineMigrations;',
            ]),
        ];

        yield 'valid: regex pattern matching any path containing config' => [
            NullViolation::create(),
            self::DEFAULT_NAMESPACE_MAPPING,
            ['/config/'],
            new RstSample([
                '.. code-block:: php',
                '',
                '    // app/config/services.php',
                '    namespace Symfony\Component\DependencyInjection\Loader\Configurator;',
            ]),
        ];

        yield 'invalid: non-ignored path still reports' => [
            Violation::from(
                'The namespace "Wrong\Namespace" does not match the filepath "src/Entity/User.php", expected namespace "Entity"',
                'filename',
                4,
                'namespace Wrong\Namespace;',
            ),
            self::DEFAULT_NAMESPACE_MAPPING,
            ['/^config\//'],
            new RstSample([
                '.. code-block:: php',
                '',
                '    // src/Entity/User.php',
                '    namespace Wrong\Namespace;',
            ]),
        ];
    }

    /**
     * @param array<string, list<string>|string> $namespaceMapping
     * @param array<int, string>                 $ignoredNamespaces
     */
    #[Test]
    #[DataProvider('checkWithIgnoredNamespacesProvider')]
    public function checkWithIgnoredNamespaces(ViolationInterface $expected, array $namespaceMapping, array $ignoredNamespaces, RstSample $sample): void
    {
        $rule = new FilepathAndNamespaceShouldMatch();
        $rule->setOptions([
            'namespace_mapping' => $namespaceMapping,
            'ignored_namespaces' => $ignoredNamespaces,
        ]);

        self::assertEquals(
            $expected,
            $rule->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    public static function checkWithIgnoredNamespacesProvider(): iterable
    {
        yield 'valid: Symfony namespace is ignored with regex' => [
            NullViolation::create(),
            self::DEFAULT_NAMESPACE_MAPPING,
            ['/^Symfony\\\\/'],
            new RstSample([
                '.. code-block:: php',
                '',
                '    // config/services.php',
                '    namespace Symfony\Component\DependencyInjection\Loader\Configurator;',
            ]),
        ];

        yield 'valid: namespace is ignored (case insensitive regex)' => [
            NullViolation::create(),
            self::DEFAULT_NAMESPACE_MAPPING,
            ['/^symfony\\\\/i'],
            new RstSample([
                '.. code-block:: php',
                '',
                '    // config/services.php',
                '    namespace Symfony\Component\DependencyInjection\Loader\Configurator;',
            ]),
        ];

        yield 'valid: multiple ignored namespaces with regex' => [
            NullViolation::create(),
            self::DEFAULT_NAMESPACE_MAPPING,
            ['/^Symfony\\\\/', '/^DoctrineMigrations$/'],
            new RstSample([
                '.. code-block:: php',
                '',
                '    // migrations/Version20200101.php',
                '    namespace DoctrineMigrations;',
            ]),
        ];

        yield 'valid: regex pattern matching namespace containing Component' => [
            NullViolation::create(),
            self::DEFAULT_NAMESPACE_MAPPING,
            ['/Component/'],
            new RstSample([
                '.. code-block:: php',
                '',
                '    // config/services.php',
                '    namespace Symfony\Component\DependencyInjection\Loader\Configurator;',
            ]),
        ];

        yield 'invalid: non-ignored namespace still reports' => [
            Violation::from(
                'The namespace "Wrong\Namespace" does not match the filepath "src/Entity/User.php", expected namespace "Entity"',
                'filename',
                4,
                'namespace Wrong\Namespace;',
            ),
            self::DEFAULT_NAMESPACE_MAPPING,
            ['/^Symfony\\\\/'],
            new RstSample([
                '.. code-block:: php',
                '',
                '    // src/Entity/User.php',
                '    namespace Wrong\Namespace;',
            ]),
        ];
    }
}
