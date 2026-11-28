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
use App\Value\RuleGroup;
use App\Value\Violation;
use App\Value\ViolationInterface;
use Symfony\Component\Finder\Finder;

#[Description('Report all .rst.inc files which are not included anywhere in the documentation.')]
final class NoUnusedIncFiles extends AbstractRule implements DirectoryContentRule
{
    public static function getGroups(): array
    {
        return [
            RuleGroup::Symfony(),
        ];
    }

    /**
     * @return ViolationInterface[]
     */
    public function check(Finder $finder, string $analyzeDir): array
    {
        $incFiles = [];
        $referencedFiles = [];

        // Collect all .rst.inc files
        $incFinder = new Finder();
        $incFinder->files()->name('*.rst.inc')->in($analyzeDir)->exclude('vendor');

        foreach ($incFinder as $file) {
            $relativePath = $file->getRelativePathname();
            $incFiles[$relativePath] = $file->getRealPath();
        }

        if ([] === $incFiles) {
            return [];
        }

        // Scan all .rst and .rst.inc files for include/literalinclude directives
        foreach ($finder as $file) {
            $content = file_get_contents($file->getRealPath());

            if (false === $content) {
                continue;
            }

            // Match include and literalinclude directives
            // Pattern: .. include:: path or .. literalinclude:: path
            if (preg_match_all('/^\.\.\s+(?:include|literalinclude)::\s*(.+)$/m', $content, $matches)) {
                foreach ($matches[1] as $includePath) {
                    $includePath = trim($includePath);

                    // Handle absolute paths (starting with /)
                    if (str_starts_with($includePath, '/')) {
                        $absolutePath = $analyzeDir.$includePath;
                    } else {
                        // Handle relative paths
                        $absolutePath = \dirname($file->getRealPath()).'/'.$includePath;
                    }

                    $realPath = realpath($absolutePath);

                    if (false !== $realPath) {
                        $referencedFiles[$realPath] = true;
                    }
                }
            }
        }

        $violations = [];

        // Find unused .inc files
        foreach ($incFiles as $relativePath => $absolutePath) {
            if (!isset($referencedFiles[$absolutePath])) {
                $violations[] = Violation::from(
                    \sprintf('The file "%s" is not included anywhere and should be removed.', $relativePath),
                    $relativePath,
                    1,
                    '',
                );
            }
        }

        return $violations;
    }
}
