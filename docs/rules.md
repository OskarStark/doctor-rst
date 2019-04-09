# Available Rules

## `american_english`

**Groups** [`@Sonata`, `@Symfony`]

### Checks

Pattern | Message
--- | ---
`/(B\|b)ehaviour(s)?/` | Please use American English for: %s

## `be_kind_to_newcomers`

  _Do not use belittling words!_

**Groups** [`@Sonata`, `@Symfony`]

### Checks

Pattern | Message
--- | ---
`/simply/i` | Please remove the word: %s
`/easy/i` | Please remove the word: %s
`/easily/i` | Please remove the word: %s
`/obviously/i` | Please remove the word: %s
`/trivial/i` | Please remove the word: %s
`/just/i` | Please remove the word: %s
`/quick/i` | Please remove the word: %s
`/of course/i` | Please remove the word: %s
`/logically/i` | Please remove the word: %s
`/clearly/i` | Please remove the word: %s
`/merely/i` | Please remove the word: %s
`/basically/i` | Please remove the word: %s

## `blank_line_after_directive`

  _Make sure you have a blank line after each directive._

**Groups** [`@Sonata`, `@Symfony`]

## `blank_line_after_filepath_in_code_block`

  _Make sure you have a blank line after a filepath in a code block. This rule respects PHP, YAML, XML and Twig._

**Groups** [`@Sonata`]

## `blank_line_after_filepath_in_php_code_block`

  _Make sure you have a blank line after a filepath in a PHP code block._

## `blank_line_after_filepath_in_twig_code_block`

  _Make sure you have a blank line after a filepath in a Twig code block._

## `blank_line_after_filepath_in_xml_code_block`

  _Make sure you have a blank line after a filepath in a XML code block._

## `blank_line_after_filepath_in_yaml_code_block`

  _Make sure you have a blank line after a filepath in a YAML code block._

## `composer_dev_option_at_the_end`

**Groups** [`@Sonata`]

## `composer_dev_option_not_at_the_end`

**Groups** [`@Symfony`]

## `correct_code_block_directive_based_on_the_content`

**Groups** [`@Sonata`, `@Symfony`]

## `ensure_order_of_code_blocks_in_configuration_block`

**Groups** [`@Sonata`, `@Symfony`]

## `even_brackets_count`

**Groups** [`@Sonata`, `@Symfony`]

## `extend_abstract_admin`

**Groups** [`@Sonata`]

## `extend_abstract_controller`

**Groups** [`@Symfony`]

## `extend_controller`

**Groups** [`@Symfony`]

## `final_admin_classes`

**Groups** [`@Sonata`]

## `final_admin_extension_classes`

**Groups** [`@Sonata`]

## `kernel_instead_of_app_kernel`

**Groups** [`@Sonata`]

## `line_length`

## `lowercase_as_in_use_statements`

**Groups** [`@Sonata`, `@Symfony`]

## `no_admin_yaml`

**Groups** [`@Sonata`]

## `no_app_bundle`

**Groups** [`@Sonata`]

## `no_app_console`

**Groups** [`@Sonata`, `@Symfony`]

## `no_bash_prompt`

**Groups** [`@Sonata`]

## `no_blank_line_after_filepath_in_code_block`

## `no_blank_line_after_filepath_in_php_code_block`

**Groups** [`@Symfony`]

## `no_blank_line_after_filepath_in_twig_code_block`

**Groups** [`@Symfony`]

## `no_blank_line_after_filepath_in_xml_code_block`

**Groups** [`@Symfony`]

## `no_blank_line_after_filepath_in_yaml_code_block`

**Groups** [`@Symfony`]

## `no_composer_phar`

**Groups** [`@Sonata`]

## `no_composer_req`

**Groups** [`@Symfony`]

## `no_config_yaml`

**Groups** [`@Sonata`, `@Symfony`]

## `no_explicit_use_of_code_block_php`

**Groups** [`@Symfony`]

## `no_inheritdoc`

**Groups** [`@Sonata`]

## `no_namespace_after_use_statements`

**Groups** [`@Sonata`, `@Symfony`]

## `no_php_open_tag_in_code_block_php_directive`

**Groups** [`@Sonata`, `@Symfony`]

## `no_php_prefix_before_bin_console`

**Groups** [`@Sonata`]

## `no_php_prefix_before_composer`

**Groups** [`@Sonata`]

## `no_space_before_self_xml_closing_tag`

**Groups** [`@Sonata`]

## `not_many_blank_lines`

**Groups** [`@Sonata`, `@Symfony`]

## `ordered_use_statements`

**Groups** [`@Sonata`, `@Symfony`]

## `php_open_tag_in_code_block_php_directive`

## `php_prefix_before_bin_console`

**Groups** [`@Symfony`]

## `replacement`

**Groups** [`@Sonata`, `@Symfony`]

### Checks

Pattern | Message
--- | ---
`/^([\s]+)?\/\/.\.(\.)?$/` | Please replace "%s" with "// ..."
`/^([\s]+)?#.\.(\.)?$/` | Please replace "%s" with "# ..."
`/^([\s]+)?<!--(.\.(\.)?\|[\s]+\.\.[\s]+)-->$/` | Please replace "%s" with "<!-- ... -->"
`/^([\s]+)?{#(.\.(\.)?\|[\s]+\.\.[\s]+)#}$/` | Please replace "%s" with "{# ... #}"
`/apps/` | Please replace "%s" with "applications"
`/Apps/` | Please replace "%s" with "Applications"
`/typehint/` | Please replace "%s" with "type-hint"
`/Typehint/` | Please replace "%s" with "Type-hint"
`/encoding="utf-8"/` | Please replace "%s" with "encoding="UTF-8""
`/\$fileSystem/` | Please replace "%s" with "$filesystem"
`/Content-type/` | Please replace "%s" with "Content-Type"

## `short_array_syntax`

**Groups** [`@Sonata`]

## `space_before_self_xml_closing_tag`

## `typo`

**Groups** [`@Sonata`, `@Symfony`]

### Checks

Pattern | Message
--- | ---
`/compsoer/i` | Typo in word "%s"
`/registerbundles\(\)/` | Typo in word "%s", use "registerBundles()"
`/retun/` | Typo in word "%s"
`/displayes/i` | Typo in word "%s"
`/mantains/i` | Typo in word "%s"
`/doctine/i` | Typo in word "%s"
`/adress/i` | Typo in word "%s"
`/argon21/` | Typo in word "%s", use "argon2i"
`/descritpion/i` | Typo in word "%s"
`/recalcuate/i` | Typo in word "%s"

## `use_deprecated_directive_instead_of_versionadded`

**Groups** [`@Sonata`, `@Symfony`]

## `use_https_xsd_urls`

**Groups** [`@Sonata`, `@Symfony`]

## `valid_use_statements`

**Groups** [`@Sonata`, `@Symfony`]

## `versionadded_directive_major_version`

**Groups** [`@Symfony`]

## `versionadded_directive_min_version`

**Groups** [`@Symfony`]

## `versionadded_directive_should_have_version`

**Groups** [`@Sonata`, `@Symfony`]

## `yaml_instead_of_yml_suffix`

**Groups** [`@Sonata`, `@Symfony`]

## `yarn_dev_option_at_the_end`

**Groups** [`@Sonata`, `@Symfony`]

## `yarn_dev_option_not_at_the_end`

