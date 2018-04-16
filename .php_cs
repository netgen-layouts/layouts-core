<?php

return PhpCsFixer\Config::create()
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'align_multiline_comment' => true,
        'array_indentation' => true,
        'array_syntax' => ['syntax' => 'long'],
        'combine_consecutive_issets' => true,
        'combine_consecutive_unsets' => true,
        'compact_nullable_typehint' => true,
        'concat_space' => ['spacing' => 'one'],
        'date_time_immutable' => true,
        'escape_implicit_backslashes' => true,
        'explicit_indirect_variable' => true,
        'explicit_string_variable' => true,
        'fully_qualified_strict_types' => true,
        'heredoc_to_nowdoc' => true,
        'list_syntax' => ['syntax' => 'long'],
        'mb_str_functions' => true,
        'multiline_comment_opening_closing' => true,
        'no_alternative_syntax' => true,
        'no_null_property_initialization' => true,
        'no_superfluous_elseif' => true,
        'no_unreachable_default_argument_value' => true,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'ordered_class_elements' => true,
        'ordered_imports' => true,
        'php_unit_ordered_covers' => true,
        'phpdoc_add_missing_param_annotation' => true,
        'phpdoc_align' => false,
        'phpdoc_order' => true,
        'psr4' => true,
        'semicolon_after_instruction' => true,
        'simplified_null_return' => true,
        'strict_comparison' => true,
        'strict_param' => true,
        'string_line_ending' => true,
        'yoda_style' => false,
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->exclude(['vendor', 'node_modules'])
            ->in(__DIR__)
    )
;
