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
final class FilepathAndNamespaceShouldMatch extends AbstractRule implements LineContentRule
{
    private const array COMMON_PREFIXES = [
        'src/',
        'lib/',
        'app/',
        'tests/',
        'bundle/',
    ];

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
            }

            // Look for namespace declaration like: namespace Acme\FooBundle\Entity;
            if (null === $namespace && $matches = $currentLine->clean()->match('/^namespace\s+([^;]+);$/')) {
                /** @var string[] $matches */
                $namespace = $matches[1];
                $namespaceLineNumber = $currentLineNumber;
            }

            // If we found both, check if they match
            if (null !== $filepath && null !== $namespace) {
                $expectedNamespace = self::extractNamespaceFromFilepath($filepath);

                if (null !== $expectedNamespace && $expectedNamespace !== $namespace) {
                    return Violation::from(
                        \sprintf(
                            'The namespace "%s" does not match the filepath "%s", expected namespace "%s"',
                            $namespace,
                            $filepath,
                            $expectedNamespace,
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

    private static function extractNamespaceFromFilepath(string $filepath): ?string
    {
        // Remove common prefixes
        $normalizedPath = $filepath;

        foreach (self::COMMON_PREFIXES as $prefix) {
            if (str_starts_with(strtolower($normalizedPath), $prefix)) {
                $normalizedPath = substr($normalizedPath, \strlen($prefix));

                break;
            }
        }

        // Remove the filename to get just the directory path
        $lastSlash = strrpos($normalizedPath, '/');

        if (false === $lastSlash) {
            // No directory part, can't determine namespace
            return null;
        }

        $directoryPath = substr($normalizedPath, 0, $lastSlash);

        // Convert path separators to namespace separators
        $namespace = str_replace('/', '\\', $directoryPath);

        // Return null if namespace is empty
        if ('' === $namespace) {
            return null;
        }

        return $namespace;
    }
}
