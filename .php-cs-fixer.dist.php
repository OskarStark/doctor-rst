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

use Ergebnis\PhpCsFixer\Config\Factory;
use Ergebnis\PhpCsFixer\Config\Rules;
use Ergebnis\PhpCsFixer\Config\RuleSet\Php84;

$header = <<<'HEADER'
This file is part of DOCtor-RST.

(c) Oskar Stark <oskarstark@googlemail.com>

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
HEADER;

$customRules = [
    'general_phpdoc_annotation_remove' => [
        'annotations' => [
            'no-named-arguments',
        ],
    ],
    'no_trailing_whitespace_in_string' => false,
];

$ruleSet = Php84::create()->withHeader($header)->withRules(Rules::fromArray(array_merge([
    'blank_line_before_statement' => [
        'statements' => [
            'break',
            'continue',
            'declare',
            'default',
            'do',
            'exit',
            'for',
            'foreach',
            'goto',
            'if',
            'include',
            'include_once',
            'require',
            'require_once',
            'return',
            'switch',
            'throw',
            'try',
            'while',
        ],
    ],
    'concat_space' => [
        'spacing' => 'none',
    ],
    'date_time_immutable' => false,
    'error_suppression' => false,
    'final_class' => false,
    'mb_str_functions' => false,
    'native_function_invocation' => [
        'exclude' => [],
        'include' => [
            '@compiler_optimized',
        ],
        'scope' => 'all',
        'strict' => false,
    ],
    'php_unit_internal_class' => false,
    'php_unit_test_annotation' => [
        'style' => 'annotation',
    ],
    'php_unit_test_class_requires_covers' => false,
    'return_to_yield_from' => false,
    'phpdoc_array_type' => false,
    'phpdoc_list_type' => false,
    'attribute_empty_parentheses' => false,
    'class_attributes_separation' => [
        'elements' => [
            'const' => 'only_if_meta',
            'property' => 'only_if_meta',
            'trait_import' => 'none',
            'case' => 'none',
        ],
    ],
    'final_public_method_for_abstract_class' => false,
    'ordered_attributes' => [
        'sort_algorithm' => 'custom',
        'order' => [
            'PHPUnit\\Framework\\Attributes\\Test',
            'PHPUnit\\Framework\\Attributes\\Testdox',
            'PHPUnit\\Framework\\Attributes\\DataProvider',
        ],
    ],
], $customRules)));

$config = Factory::fromRuleSet($ruleSet);

$config->getFinder()
    ->append([
        __DIR__.'/rector.php',
        __DIR__.'/composer-unused.php',
        __DIR__.'/.php-cs-fixer.dist.php',
    ])
    ->in('config')
    ->in('src')
    ->in('tests');

return $config;
