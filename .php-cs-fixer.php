<?php

return ( new PhpCsFixer\Config() )
	->setIndent( "\t" )
	->setLineEnding( "\n" )
	->setRules(
		array(
			'array_syntax'                                    => array( 'syntax' => 'long' ),
			'array_indentation'                               => true,
			'binary_operator_spaces'                          => array( 'default' => 'align_single_space_minimal' ),
			'blank_line_after_opening_tag'                    => true,
			'blank_line_between_import_groups'                => true,
			'blank_lines_before_namespace'                    => true,
			'braces'                                          => array(
				'position_after_functions_and_oop_constructs' => 'same',
				'position_after_control_structures'           => 'same',
				'position_after_anonymous_constructs'         => 'same',
			),
			'class_definition'                                => array( 'single_line' => false, 'space_before_parenthesis' => true ),
			'compact_nullable_type_declaration'               => true,
			'declare_equal_normalize'                         => true,
			'lowercase_cast'                                  => true,
			'lowercase_static_reference'                      => true,
			'new_with_braces'                                 => true,
			'no_blank_lines_after_class_opening'              => true,
			'no_leading_import_slash'                         => true,
			'no_whitespace_in_blank_line'                     => true,
			'ordered_class_elements'                          => array( 'order' => array( 'use_trait' ) ),
			'ordered_imports'                                 => array( 'imports_order' => array( 'class', 'function', 'const' ), 'sort_algorithm' => 'none' ),
			'return_type_declaration'                         => true,
			'short_scalar_cast'                               => true,
			'single_import_per_statement'                     => array( 'group_to_single_imports' => false ),
			'single_trait_insert_per_statement'               => true,
			'ternary_operator_spaces'                         => true,
			'unary_operator_spaces'                           => array( 'only_dec_inc' => true ),
			'visibility_required'                             => true,
			'blank_line_after_namespace'                      => true,
			'constant_case'                                   => true,
			'control_structure_continuation_position'         => true,
			'elseif'                                          => true,
			'function_declaration'                            => true,
			'indentation_type'                                => true,
			'line_ending'                                     => true,
			'lowercase_keywords'                              => true,
			'method_argument_space'                           => array(
				'on_multiline'                      => 'ignore',
				'after_heredoc'                     => true,
				'keep_multiple_spaces_after_comma'  => false,
			),
			'no_break_comment'                                => true,
			'no_closing_tag'                                  => true,
			'no_multiple_statements_per_line'                 => true,
			'no_space_around_double_colon'                    => true,
			'no_spaces_after_function_name'                   => true,
			'no_trailing_whitespace'                          => true,
			'no_trailing_whitespace_in_comment'               => true,
			'not_operator_with_space'                         => true,
			'single_blank_line_at_eof'                        => true,
			'single_class_element_per_statement'              => array( 'elements' => array( 'property' ) ),
			'single_line_after_imports'                       => true,
			'spaces_inside_parentheses'                       => array( 'space' => 'single' ),
			'statement_indentation'                           => true,
			'switch_case_semicolon_to_colon'                  => true,
			'switch_case_space'                               => true,
			'encoding'                                        => true,
			'full_opening_tag'                                => true,
			'phpdoc_to_comment'                               => false,
			'phpdoc_summary'                                  => true,
			'include'                                         => true,
			'phpdoc_no_empty_return'                          => true,
			'phpdoc_no_useless_inheritdoc'                    => true,
			'phpdoc_order'                                    => true,
			'phpdoc_scalar'                                   => true,
			'phpdoc_align'                                    => array( 'tags' => array( 'param' ) ),
			'phpdoc_separation'                               => true,
			'phpdoc_single_line_var_spacing'                  => true,
			'phpdoc_trim'                                     => true,
			'phpdoc_trim_consecutive_blank_line_separation'   => true,
			'phpdoc_types'                                    => true,
			'phpdoc_var_without_name'                         => true,
			'yoda_style'                                      => false,
		)
	)
	->setFinder(
		PhpCsFixer\Finder::create()
			->in( __DIR__ )
			->exclude( 'vendor' )
			->exclude( 'templates' )
	);
