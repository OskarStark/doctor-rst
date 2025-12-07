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

use App\Attribute\Rule\Description;
use App\Attribute\Rule\InvalidExample;
use App\Attribute\Rule\ValidExample;
use App\Rst\RstParser;
use App\Value\Lines;
use App\Value\NullViolation;
use App\Value\RuleGroup;
use App\Value\Violation;
use App\Value\ViolationInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

#[Description('Ensures the namespace in a PHP code block matches the filepath.')]
#[InvalidExample(<<<'RST'
.. code-block:: php

    // src/Acme/FooBundle/Entity/User.php
    namespace Acme\WrongBundle\Entity;
RST)]
#[ValidExample(<<<'RST'
.. code-block:: php

    // src/Acme/FooBundle/Entity/User.php
    namespace Acme\FooBundle\Entity;
RST)]
final class FilepathAndNamespaceShouldMatch extends AbstractRule implements Configurable, LineContentRule
{
    /**
     * Maps filepath prefixes to namespace prefixes.
     * For example: ['src/' => 'App\\'] means src/Form/Foo.php -> App\Form.
     * Can also be an array: ['src/' => ['App\\', 'Acme\\']] accepts both.
     *
     * @var array<string, list<string>|string>
     */
    private array $namespaceMapping;

    /**
     * Regex patterns for filepaths to ignore.
     * For example: ['/^config\//'] means config/services.php will be skipped.
     *
     * @var array<int, string>
     */
    private array $ignoredPaths = [];

    /**
     * Regex patterns for namespaces to ignore.
     * For example: ['/^Symfony\\\\/'] means namespaces starting with Symfony\ will be skipped.
     *
     * @var array<int, string>
     */
    private array $ignoredNamespaces = [];

    public function configureOptions(OptionsResolver $resolver): OptionsResolver
    {
        $resolver
            ->setRequired('namespace_mapping')
            ->setAllowedTypes('namespace_mapping', 'array')
            ->setDefault('ignored_paths', [])
            ->setAllowedTypes('ignored_paths', 'array')
            ->setDefault('ignored_namespaces', [])
            ->setAllowedTypes('ignored_namespaces', 'array');

        return $resolver;
    }

    public function setOptions(array $options): void
    {
        $resolver = $this->configureOptions(new OptionsResolver());

        $resolvedOptions = $resolver->resolve($options);

        /** @phpstan-ignore assign.propertyType */
        $this->namespaceMapping = $resolvedOptions['namespace_mapping'];
        /** @phpstan-ignore assign.propertyType */
        $this->ignoredPaths = $resolvedOptions['ignored_paths'];
        /** @phpstan-ignore assign.propertyType */
        $this->ignoredNamespaces = $resolvedOptions['ignored_namespaces'];
    }

    public static function getGroups(): array
    {
        return [
            RuleGroup::Sonata(),
            RuleGroup::Symfony(),
        ];
    }

    public function check(Lines $lines, int $number, string $filename): ViolationInterface
    {
        $lines->seek($number);
        $line = $lines->current();

        if (!RstParser::isPhpDirective($line)) {
            return NullViolation::create();
        }

        $indention = $line->indention();

        $lines->next();

        $filepath = null;
        $namespace = null;
        $namespaceLineNumber = null;
        $currentLineNumber = $number;

        while ($lines->valid()
            && !$lines->current()->isDirective()
            && ($lines->current()->indention() > $indention || $lines->current()->isBlank())
        ) {
            $currentLine = $lines->current();
            ++$currentLineNumber;

            // Look for filepath comment like: // src/Acme/FooBundle/Entity/User.php
            if (null === $filepath && $matches = $currentLine->clean()->match('/^\/\/\s*(.+\.php)$/')) {
                /** @var string[] $matches */
                $filepath = $matches[1];

                // Check if filepath should be ignored
                if ($this->isIgnoredPath($filepath)) {
                    return NullViolation::create();
                }
            }

            // Look for namespace declaration like: namespace Acme\FooBundle\Entity;
            if (null === $namespace && $matches = $currentLine->clean()->match('/^namespace\s+([^;]+);$/')) {
                /** @var string[] $matches */
                $namespace = $matches[1];
                $namespaceLineNumber = $currentLineNumber;

                // Check if namespace should be ignored
                if ($this->isIgnoredNamespace($namespace)) {
                    return NullViolation::create();
                }
            }

            // If we found both, check if they match
            if (null !== $filepath && null !== $namespace) {
                $expectedNamespaces = $this->extractNamespacesFromFilepath($filepath);

                if ([] !== $expectedNamespaces && !\in_array($namespace, $expectedNamespaces, true)) {
                    return Violation::from(
                        \sprintf(
                            'The namespace "%s" does not match the filepath "%s", expected namespace "%s"',
                            $namespace,
                            $filepath,
                            implode('" or "', $expectedNamespaces),
                        ),
                        $filename,
                        $namespaceLineNumber + 1,
                        $currentLine,
                    );
                }

                // Found both, and they match or can't be compared, done with this block
                return NullViolation::create();
            }

            $lines->next();
        }

        return NullViolation::create();
    }

    private function isIgnoredPath(string $filepath): bool
    {
        foreach ($this->ignoredPaths as $pattern) {
            if (preg_match($pattern, $filepath)) {
                return true;
            }
        }

        return false;
    }

    private function isIgnoredNamespace(string $namespace): bool
    {
        foreach ($this->ignoredNamespaces as $pattern) {
            if (preg_match($pattern, $namespace)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return list<string>
     */
    private function extractNamespacesFromFilepath(string $filepath): array
    {
        $normalizedPath = $filepath;
        $namespacePrefixes = [];

        // Find matching mapping and apply it
        foreach ($this->namespaceMapping as $pathPrefix => $nsPrefixes) {
            if (str_starts_with(strtolower($normalizedPath), strtolower($pathPrefix))) {
                $normalizedPath = substr($normalizedPath, \strlen($pathPrefix));
                $namespacePrefixes = \is_array($nsPrefixes) ? $nsPrefixes : [$nsPrefixes];

                break;
            }
        }

        // Remove the filename to get just the directory path
        $lastSlash = strrpos($normalizedPath, '/');

        if (false === $lastSlash) {
            // No directory part
            // If we have namespace prefixes, return them (trimmed of trailing backslash)
            if ([] !== $namespacePrefixes) {
                return array_map(static fn (string $prefix): string => rtrim($prefix, '\\'), $namespacePrefixes);
            }

            // No namespace prefix and no directory part, can't determine namespace
            return [];
        }

        $directoryPath = substr($normalizedPath, 0, $lastSlash);

        // Convert path separators to namespace separators
        $namespaceSuffix = str_replace('/', '\\', $directoryPath);

        // Combine prefix and suffix
        if ([] !== $namespacePrefixes) {
            // namespacePrefixes already end with \ (e.g., "App\")
            return array_map(static fn (string $prefix): string => $prefix.$namespaceSuffix, $namespacePrefixes);
        }

        // Return empty array if namespace is empty
        if ('' === $namespaceSuffix) {
            return [];
        }

        return [$namespaceSuffix];
    }
}
