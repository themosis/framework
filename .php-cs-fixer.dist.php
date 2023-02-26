<?php

use PhpCsFixer\Finder;

/**
 * Plugin PHP code fixer.
 */
$finder = Finder::create()
    ->in([
        'database',
        'src',
        'tests'
    ])
    ->exclude([
        'bin',
        'node_modules'
    ]);

$config = new PhpCsFixer\Config();

return $config->setRules([
        '@PSR12' => true,
        'strict_param' => false,
        'array_syntax' => [
            'syntax' => 'short'
        ],
        'cast_spaces' => [
            'space' => 'single'
        ],
        'concat_space' => [
            'spacing' => 'one'
        ],
        'ordered_imports' => true,
        'no_unused_imports' => true,
        'no_whitespace_before_comma_in_array' => true,
        'blank_line_before_statement' => true,
        'no_whitespace_in_blank_line' => true,
        'blank_line_after_namespace' => true,
        'single_blank_line_before_namespace' => true,
        'single_line_after_imports' => true,
        'blank_line_after_opening_tag' => true,
        'no_empty_statement' => true,
        'trailing_comma_in_multiline' => ['elements' => ['arrays', 'arguments', 'parameters']],
        'no_blank_lines_after_class_opening' => true,
        'no_blank_lines_after_phpdoc' => true,
        'phpdoc_trim' => true,
        'phpdoc_add_missing_param_annotation' => true,
        'phpdoc_align' => true,
        'phpdoc_no_empty_return' => true,
        'phpdoc_order' => true,
        'phpdoc_return_self_reference' => true,
        'phpdoc_scalar' => true,
        'phpdoc_separation' => true,
        'phpdoc_single_line_var_spacing' => true,
        'phpdoc_var_without_name' => true,
        'not_operator_with_successor_space' => true,
        'return_type_declaration' => [
            'space_before' => 'none'
        ],
        'semicolon_after_instruction' => true,
        'trim_array_spaces' => true,
        'ternary_operator_spaces' => true
    ])
        ->setFinder($finder);
