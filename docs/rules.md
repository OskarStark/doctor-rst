# Rules Overview

* [american_english](#american_english)
* [argument_variable_must_match_type](#argument_variable_must_match_type)
* [avoid_repetetive_words](#avoid_repetetive_words)
* [be_kind_to_newcomers](#be_kind_to_newcomers) :exclamation:
* [blank_line_after_anchor](#blank_line_after_anchor)
* [blank_line_after_colon](#blank_line_after_colon)
* [blank_line_after_directive](#blank_line_after_directive)
* [blank_line_after_filepath_in_code_block](#blank_line_after_filepath_in_code_block)
* [blank_line_after_filepath_in_php_code_block](#blank_line_after_filepath_in_php_code_block)
* [blank_line_after_filepath_in_twig_code_block](#blank_line_after_filepath_in_twig_code_block)
* [blank_line_after_filepath_in_xml_code_block](#blank_line_after_filepath_in_xml_code_block)
* [blank_line_after_filepath_in_yaml_code_block](#blank_line_after_filepath_in_yaml_code_block)
* [blank_line_before_directive](#blank_line_before_directive)
* [composer_dev_option_at_the_end](#composer_dev_option_at_the_end)
* [composer_dev_option_not_at_the_end](#composer_dev_option_not_at_the_end)
* [correct_code_block_directive_based_on_the_content](#correct_code_block_directive_based_on_the_content)
* [deprecated_directive_major_version](#deprecated_directive_major_version)
* [deprecated_directive_min_version](#deprecated_directive_min_version)
* [deprecated_directive_should_have_version](#deprecated_directive_should_have_version)
* [ensure_bash_prompt_before_composer_command](#ensure_bash_prompt_before_composer_command)
* [ensure_exactly_one_space_before_directive_type](#ensure_exactly_one_space_before_directive_type)
* [ensure_exactly_one_space_between_link_definition_and_link](#ensure_exactly_one_space_between_link_definition_and_link)
* [ensure_github_directive_start_with_prefix](#ensure_github_directive_start_with_prefix)
* [ensure_link_bottom](#ensure_link_bottom)
* [ensure_link_definition_contains_valid_url](#ensure_link_definition_contains_valid_url)
* [ensure_order_of_code_blocks_in_configuration_block](#ensure_order_of_code_blocks_in_configuration_block)
* [ensure_php_reference_syntax](#ensure_php_reference_syntax)
* [extend_abstract_admin](#extend_abstract_admin)
* [extend_abstract_controller](#extend_abstract_controller)
* [extend_controller](#extend_controller)
* [extension_xlf_instead_of_xliff](#extension_xlf_instead_of_xliff)
* [filename_uses_dashes_only](#filename_uses_dashes_only)
* [filename_uses_underscores_only](#filename_uses_underscores_only)
* [final_admin_classes](#final_admin_classes)
* [final_admin_extension_classes](#final_admin_extension_classes)
* [forbidden_directives](#forbidden_directives)
* [indention](#indention) :exclamation:
* [kernel_instead_of_app_kernel](#kernel_instead_of_app_kernel)
* [line_length](#line_length)
* [lowercase_as_in_use_statements](#lowercase_as_in_use_statements)
* [max_blank_lines](#max_blank_lines)
* [max_colons](#max_colons)
* [no_admin_yaml](#no_admin_yaml)
* [no_app_bundle](#no_app_bundle)
* [no_app_console](#no_app_console)
* [no_bash_prompt](#no_bash_prompt)
* [no_blank_line_after_filepath_in_code_block](#no_blank_line_after_filepath_in_code_block)
* [no_blank_line_after_filepath_in_php_code_block](#no_blank_line_after_filepath_in_php_code_block)
* [no_blank_line_after_filepath_in_twig_code_block](#no_blank_line_after_filepath_in_twig_code_block)
* [no_blank_line_after_filepath_in_xml_code_block](#no_blank_line_after_filepath_in_xml_code_block)
* [no_blank_line_after_filepath_in_yaml_code_block](#no_blank_line_after_filepath_in_yaml_code_block)
* [no_brackets_in_method_directive](#no_brackets_in_method_directive)
* [no_composer_phar](#no_composer_phar)
* [no_composer_req](#no_composer_req)
* [no_config_yaml](#no_config_yaml)
* [no_contraction](#no_contraction)
* [no_directive_after_shorthand](#no_directive_after_shorthand)
* [no_duplicate_use_statements](#no_duplicate_use_statements)
* [no_explicit_use_of_code_block_php](#no_explicit_use_of_code_block_php)
* [no_footnotes](#no_footnotes)
* [no_inheritdoc_in_code_examples](#no_inheritdoc_in_code_examples)
* [no_merge_conflict](#no_merge_conflict)
* [no_namespace_after_use_statements](#no_namespace_after_use_statements)
* [no_php_open_tag_in_code_block_php_directive](#no_php_open_tag_in_code_block_php_directive)
* [no_php_prefix_before_bin_console](#no_php_prefix_before_bin_console)
* [no_php_prefix_before_composer](#no_php_prefix_before_composer)
* [no_space_before_self_xml_closing_tag](#no_space_before_self_xml_closing_tag)
* [only_backslashes_in_namespace_in_php_code_block](#only_backslashes_in_namespace_in_php_code_block)
* [only_backslashes_in_use_statements_in_php_code_block](#only_backslashes_in_use_statements_in_php_code_block)
* [ordered_use_statements](#ordered_use_statements)
* [php_open_tag_in_code_block_php_directive](#php_open_tag_in_code_block_php_directive)
* [php_prefix_before_bin_console](#php_prefix_before_bin_console)
* [remove_trailing_whitespace](#remove_trailing_whitespace)
* [replace_code_block_types](#replace_code_block_types)
* [replacement](#replacement)
* [short_array_syntax](#short_array_syntax)
* [space_before_self_xml_closing_tag](#space_before_self_xml_closing_tag)
* [space_between_label_and_link_in_doc](#space_between_label_and_link_in_doc)
* [space_between_label_and_link_in_ref](#space_between_label_and_link_in_ref)
* [string_replacement](#string_replacement)
* [title_underline_length_must_match_title_length](#title_underline_length_must_match_title_length)
* [typo](#typo)
* [unused_links](#unused_links)
* [use_deprecated_directive_instead_of_versionadded](#use_deprecated_directive_instead_of_versionadded)
* [use_https_xsd_urls](#use_https_xsd_urls)
* [use_named_constructor_without_new_keyword_rule](#use_named_constructor_without_new_keyword_rule)
* [valid_inline_highlighted_namespaces](#valid_inline_highlighted_namespaces)
* [valid_use_statements](#valid_use_statements)
* [versionadded_directive_major_version](#versionadded_directive_major_version)
* [versionadded_directive_min_version](#versionadded_directive_min_version)
* [versionadded_directive_should_have_version](#versionadded_directive_should_have_version)
* [yaml_instead_of_yml_suffix](#yaml_instead_of_yml_suffix)
* [yarn_dev_option_at_the_end](#yarn_dev_option_at_the_end)
* [yarn_dev_option_not_at_the_end](#yarn_dev_option_not_at_the_end)
## `american_english`

  > _Ensure only American English is used._

#### Groups [`@Sonata`, `@Symfony`]

#### Checks

Pattern | Message
--- | ---
`/(B\|b)ehaviour(s)?/` | Please use American English for: %s
`/(I\|i)nitialise/i` | Please use American English for: %s
`/normalise/i` | Please use American English for: %s
`/organise/i` | Please use American English for: %s
`/recognise/i` | Please use American English for: %s
`/centre/i` | Please use American English for: %s
`/colour/i` | Please use American English for: %s
`/flavour/i` | Please use American English for: %s
`/licence/i` | Please use American English for: %s

##### Valid Examples :+1:

```rst
This is a nice behavior...
```

##### Invalid Examples :-1:

```rst
This is a nice behaviour...
```

## `argument_variable_must_match_type`

  > _Make sure argument variable name match for type_

#### Groups [`@Symfony`]

#### Configuration options

Name | Required | Allowed Types | Default
--- | --- | --- | ---
`arguments` | `false` | `array` | `[]`

## `avoid_repetetive_words`

  > _Make sure that a word is not used twice in a row._

#### Groups [`@Sonata`, `@Symfony`]

##### Valid Examples :+1:

```rst
Please do not use it this way...
```

##### Invalid Examples :-1:

```rst
Please do not not use it this way...
```

## `be_kind_to_newcomers`

  > _Do not use belittling words!_

#### Groups [`@Experimental`]

#### Checks

Pattern | Message
--- | ---
`/simply/i` | Please remove the word: %s
`/easy/i` | Please remove the word: %s
`/easily/i` | Please remove the word: %s
`/obvious/i` | Please remove the word: %s
`/trivial/i` | Please remove the word: %s
`/of course/i` | Please remove the word: %s
`/logically/i` | Please remove the word: %s
`/merely/i` | Please remove the word: %s
`/basic/i` | Please remove the word: %s

## `blank_line_after_anchor`

  > _Make sure you have a blank line after anchor (`.. anchor:`)._

#### Groups [`@Sonata`, `@Symfony`]

## `blank_line_after_colon`

  > _Make sure you have a blank line after a sentence which ends with a colon (`:`)._

#### Groups [`@Sonata`, `@Symfony`]

## `blank_line_after_directive`

  > _Make sure you have a blank line after each directive._

#### Groups [`@Sonata`, `@Symfony`]

## `blank_line_after_filepath_in_code_block`

  > _Make sure you have a blank line after a filepath in a code block. This rule respects PHP, YAML, XML and Twig._

#### Groups [`@Sonata`, `@Symfony`]

## `blank_line_after_filepath_in_php_code_block`

  > _Make sure you have a blank line after a filepath in a PHP code block._

## `blank_line_after_filepath_in_twig_code_block`

  > _Make sure you have a blank line after a filepath in a Twig code block._

## `blank_line_after_filepath_in_xml_code_block`

  > _Make sure you have a blank line after a filepath in a XML code block._

## `blank_line_after_filepath_in_yaml_code_block`

  > _Make sure you have a blank line after a filepath in a YAML code block._

## `blank_line_before_directive`

  > _Make sure you have a blank line before each directive._

#### Groups [`@Sonata`, `@Symfony`]

## `composer_dev_option_at_the_end`

  > _Make sure Composer `--dev` option for `require` command is used at the end._

#### Groups [`@Sonata`]

##### Valid Examples :+1:

```rst
composer require symfony/var-dumper --dev
```

##### Invalid Examples :-1:

```rst
composer require --dev symfony/var-dumper
```

## `composer_dev_option_not_at_the_end`

  > _Make sure Composer `--dev` option for `require` command is not used at the end._

#### Groups [`@Symfony`]

##### Valid Examples :+1:

```rst
composer require --dev symfony/var-dumper
```

##### Invalid Examples :-1:

```rst
composer require symfony/var-dumper --dev
```

## `correct_code_block_directive_based_on_the_content`

#### Groups [`@Sonata`, `@Symfony`]

## `deprecated_directive_major_version`

#### Groups [`@Symfony`]

#### Configuration options

Name | Required
--- | ---
`major_version` | `true` | `int`

## `deprecated_directive_min_version`

#### Groups [`@Symfony`]

#### Configuration options

Name | Required
--- | ---
`min_version` | `true` | `string`

## `deprecated_directive_should_have_version`

  > _Ensure a deprecated directive has a version which follows SemVer._

#### Groups [`@Sonata`, `@Symfony`]

##### Valid Examples :+1:

```rst
.. deprecated:: 3.4
```

##### Invalid Examples :-1:

```rst
.. deprecated::
```

```rst
.. deprecated:: foo-bar
```

## `ensure_bash_prompt_before_composer_command`

  > _Make sure Composer command in a terminal/bash code block is prefixed with a $._

#### Groups [`@Symfony`]

##### Valid Examples :+1:

```rst
$ composer require symfony/var-dumper
```

##### Invalid Examples :-1:

```rst
composer require symfony/var-dumper
```

## `ensure_exactly_one_space_before_directive_type`

  > _Ensure exactly one space before directive type._

#### Groups [`@Symfony`]

##### Valid Examples :+1:

```rst
.. code-block:: php
```

##### Invalid Examples :-1:

```rst
..  code-block:: php
```

## `ensure_exactly_one_space_between_link_definition_and_link`

  > _Ensure exactly one space between link definition and link._

#### Groups [`@Sonata`, `@Symfony`]

##### Valid Examples :+1:

```rst
.. _DOCtor-RST: https://github.com/OskarStark/DOCtor-RST
```

##### Invalid Examples :-1:

```rst
.. _DOCtor-RST:     https://github.com/OskarStark/DOCtor-RST
```

## `ensure_github_directive_start_with_prefix`

#### Configuration options

Name | Required
--- | ---
`prefix` | `true` | `string`

## `ensure_link_bottom`

  > _Ensure link lines are at the bottom of the file._

#### Groups [`@Symfony`]

## `ensure_link_definition_contains_valid_url`

  > _Ensure link definition contains valid link._

#### Groups [`@Sonata`, `@Symfony`]

##### Valid Examples :+1:

```rst
.. _DOCtor-RST: https://github.com/OskarStark/DOCtor-RST
```

##### Invalid Examples :-1:

```rst
.. _DOCtor-RST: htt//github.com/OskarStark/DOCtor-RST
```

## `ensure_order_of_code_blocks_in_configuration_block`

#### Groups [`@Sonata`, `@Symfony`]

## `ensure_php_reference_syntax`

  > _Ensure php reference syntax is valid._

#### Groups [`@Sonata`, `@Symfony`]

##### Valid Examples :+1:

```rst
The :class:`Symfony\Component\Notifier\Transport` class
```

##### Invalid Examples :-1:

```rst
The :class:`Symfony\Component\Notifier\Transport`` class
```

## `extend_abstract_admin`

  > _Ensure `AbstractAdmin` and the corresponding namespace `Sonata\AdminBundle\Admin\AbstractAdmin` is used._

#### Groups [`@Sonata`]

## `extend_abstract_controller`

  > _Ensure `AbstractController` and the corresponding namespace `Symfony\Bundle\FrameworkBundle\Controller\AbstractController` is used. Instead of `Symfony\Bundle\FrameworkBundle\Controller\Controller`._

#### Groups [`@Symfony`]

## `extend_controller`

  > _Ensure `Controller` and the corresponding namespace `Symfony\Bundle\FrameworkBundle\Controller\Controller` is used. Instead of `Symfony\Bundle\FrameworkBundle\Controller\AbstractController`._

#### Groups [`@Symfony`]

## `extension_xlf_instead_of_xliff`

  > _Make sure to only use `.xlf` instead of `.xliff`._

#### Groups [`@Sonata`, `@Symfony`]

##### Valid Examples :+1:

```rst
messages.xlf
```

##### Invalid Examples :-1:

```rst
messages.xliff
```

## `filename_uses_dashes_only`

  > _Ensures a filename uses only dashes (`-`), but are allowed to start with underscore (`_`). It is a common practice to prefix included files with underscores (`_`)._

#### Groups [`@Sonata`, `@Symfony`]

##### Valid Examples :+1:

```rst
custom-extensions.rst
```

```rst
_custom-extensions.rst
```

##### Invalid Examples :-1:

```rst
custom_extensions.rst
```

## `filename_uses_underscores_only`

  > _Ensures a filename uses only underscores (`_`)._

#### Groups [`@Sonata`, `@Symfony`]

##### Valid Examples :+1:

```rst
custom_extensions.rst
```

```rst
_custom_extensions.rst
```

##### Invalid Examples :-1:

```rst
custom-extensions.rst
```

## `final_admin_classes`

#### Groups [`@Sonata`]

## `final_admin_extension_classes`

#### Groups [`@Sonata`]

## `forbidden_directives`

  > _Make sure forbidden directives are not used_

#### Groups [`@Symfony`]

#### Configuration options

Name | Required | Allowed Types | Default
--- | --- | --- | ---
`directives` | `false` | `string[]` | `[]`

## `indention`

#### Groups [`@Experimental`]

#### Configuration options

Name | Required | Allowed Types | Default
--- | --- | --- | ---
`size` | `false` | `int` | `4`

## `kernel_instead_of_app_kernel`

#### Groups [`@Sonata`]

## `line_length`

#### Configuration options

Name | Required | Allowed Types | Default
--- | --- | --- | ---
`max` | `false` | `int` | `80`

## `lowercase_as_in_use_statements`

#### Groups [`@Sonata`, `@Symfony`]

## `max_blank_lines`

#### Groups [`@Sonata`, `@Symfony`]

#### Configuration options

Name | Required | Allowed Types | Default
--- | --- | --- | ---
`max` | `false` | `int` | `2`

## `max_colons`

  > _Make sure you have max 2 colons (`::`)._

#### Groups [`@Sonata`, `@Symfony`]

##### Valid Examples :+1:

```rst
composer require symfony/var-dumper --dev
```

##### Invalid Examples :-1:

```rst
composer require --dev symfony/var-dumper
```

## `no_admin_yaml`

#### Groups [`@Sonata`]

## `no_app_bundle`

#### Groups [`@Sonata`]

## `no_app_console`

#### Groups [`@Sonata`, `@Symfony`]

## `no_bash_prompt`

  > _Ensure no bash prompt `$` is used before commands in `bash`, `shell` or `terminal` code blocks._

#### Groups [`@Sonata`]

##### Valid Examples :+1:

```rst
bin/console list
```

##### Invalid Examples :-1:

```rst
$ bin/console list
```

## `no_blank_line_after_filepath_in_code_block`

## `no_blank_line_after_filepath_in_php_code_block`

#### Groups [`@Symfony`]

## `no_blank_line_after_filepath_in_twig_code_block`

#### Groups [`@Symfony`]

## `no_blank_line_after_filepath_in_xml_code_block`

#### Groups [`@Symfony`]

## `no_blank_line_after_filepath_in_yaml_code_block`

#### Groups [`@Symfony`]

## `no_brackets_in_method_directive`

  > _Ensure a :method: directive has special format._

#### Groups [`@Sonata`, `@Symfony`]

##### Valid Examples :+1:

```rst
:method:`Symfony\\Component\\OptionsResolver\\Options::offsetGet`
```

##### Invalid Examples :-1:

```rst
:method:`Symfony\\Component\\OptionsResolver\\Options::offsetGet()`
```

## `no_composer_phar`

#### Groups [`@Sonata`]

## `no_composer_req`

#### Groups [`@Symfony`]

## `no_config_yaml`

#### Groups [`@Sonata`, `@Symfony`]

## `no_contraction`

  > _Ensure contractions are not used._

#### Groups [`@Symfony`]

#### Checks

Pattern | Message
--- | ---
`/(^\|[^[:alnum:]])(?<contraction>i\'m)/i` | Please do not use contraction for: %s
`/(^\|[^[:alnum:]])(?<contraction>(you\|we\|they)\'re)/i` | Please do not use contraction for: %s
`/(^\|[^[:alnum:]])(?<contraction>(he\|she\|it)\'s)/i` | Please do not use contraction for: %s
`/(^\|[^[:alnum:]])(?<contraction>(you\|we\|they)\'ve)/i` | Please do not use contraction for: %s
`/(^\|[^[:alnum:]])(?<contraction>(i\|you\|he\|she\|it\|we\|they)\'ll)/i` | Please do not use contraction for: %s
`/(^\|[^[:alnum:]])(?<contraction>(i\|you\|he\|she\|it\|we\|they)\'d)/i` | Please do not use contraction for: %s
`/(^\|[^[:alnum:]])(?<contraction>(aren\|can\|couldn\|didn\|hasn\|haven\|isn\|mustn\|shan\|shouldn\|wasn\|weren\|won\|wouldn)\'t)/i` | Please do not use contraction for: %s

##### Valid Examples :+1:

```rst
It is an example
```

##### Invalid Examples :-1:

```rst
It's an example
```

## `no_directive_after_shorthand`

  > _Ensure that no directive follows the shorthand `::`. This could lead to broken markup._

#### Groups [`@Sonata`, `@Symfony`]

## `no_duplicate_use_statements`

  > _Ensure there is not same use statement twice_

#### Groups [`@Symfony`]

## `no_explicit_use_of_code_block_php`

#### Groups [`@Symfony`]

## `no_footnotes`

  > _Make sure there is no footnotes_

#### Groups [`@Symfony`]

##### Invalid Examples :-1:

```rst
.. [5] A numerical footnote. Note
```

## `no_inheritdoc_in_code_examples`

#### Groups [`@Sonata`, `@Symfony`]

## `no_merge_conflict`

  > _Ensure that the files does not contain merge conflicts._

#### Groups [`@Symfony`]

## `no_namespace_after_use_statements`

#### Groups [`@Sonata`, `@Symfony`]

## `no_php_open_tag_in_code_block_php_directive`

#### Groups [`@Sonata`, `@Symfony`]

## `no_php_prefix_before_bin_console`

  > _Ensure `bin/console` is not prefixed with `php`._

#### Groups [`@Sonata`]

##### Valid Examples :+1:

```rst
bin/console list
```

##### Invalid Examples :-1:

```rst
php bin/console list
```

## `no_php_prefix_before_composer`

#### Groups [`@Sonata`]

## `no_space_before_self_xml_closing_tag`

#### Groups [`@Sonata`]

## `only_backslashes_in_namespace_in_php_code_block`

  > _A namespace declaration in a PHP code-block should only contain backslashes._

#### Groups [`@Sonata`, `@Symfony`]

##### Valid Examples :+1:

```rst
namespace Foo\Bar;
```

##### Invalid Examples :-1:

```rst
namespace Foo/Bar;
```

## `only_backslashes_in_use_statements_in_php_code_block`

  > _A use statement in a PHP code-block should only contain backslashes._

#### Groups [`@Sonata`, `@Symfony`]

##### Valid Examples :+1:

```rst
use Foo\Bar;
```

##### Invalid Examples :-1:

```rst
use Foo/Bar;
```

## `ordered_use_statements`

#### Groups [`@Sonata`, `@Symfony`]

## `php_open_tag_in_code_block_php_directive`

## `php_prefix_before_bin_console`

  > _Ensure `bin/console` is prefixed with `php` to be safe executable on Microsoft Windows._

#### Groups [`@Symfony`]

##### Valid Examples :+1:

```rst
php bin/console list
```

##### Invalid Examples :-1:

```rst
bin/console list
```

## `remove_trailing_whitespace`

  > _Make sure there is not trailing whitespace._

#### Groups [`@Symfony`]

##### Valid Examples :+1:

```rst
Valid sentence
```

##### Invalid Examples :-1:

```rst
Invalid sentence 
```

## `replace_code_block_types`

  > _Propose alternatives for disallowed code block types._

#### Groups [`@Sonata`, `@Symfony`]

#### Checks

Pattern | Message
--- | ---
`jinja` | Please do not use type "jinja" for code-block, use "twig" instead
`html+jinja` | Please do not use type "html+jinja" for code-block, use "html+twig" instead
`js` | Please do not use type "js" for code-block, use "javascript" instead

## `replacement`

#### Groups [`@Sonata`, `@Symfony`]

#### Checks

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
`/\-\-env prod/` | Please replace "%s" with "--env=prod"
`/\-\-env test/` | Please replace "%s" with "--env=test"
`/End 2 End/i` | Please replace "%s" with "End-to-End"
`/E2E/` | Please replace "%s" with "End-to-End"
`/informations/` | Please replace "%s" with "information"
`/Informations/` | Please replace "%s" with "Information"
`/performances/` | Please replace "%s" with "performance"
`/Performances/` | Please replace "%s" with "Performance"
`/``'%kernel.debug%'``/` | Please replace "%s" with "``%%kernel.debug%%``"
`/PHPdoc/` | Please replace "%s" with "PHPDoc"
`/eg\./` | Please replace "%s" with "e.g."

## `short_array_syntax`

#### Groups [`@Sonata`]

## `space_before_self_xml_closing_tag`

## `space_between_label_and_link_in_doc`

  > _Ensure a space between label and link in :doc: directive._

#### Groups [`@Sonata`, `@Symfony`]

##### Valid Examples :+1:

```rst
:doc:`File </reference/constraints/File>`
```

##### Invalid Examples :-1:

```rst
:doc:`File</reference/constraints/File>`
```

## `space_between_label_and_link_in_ref`

  > _Ensure a space between label and link in :ref: directive._

#### Groups [`@Sonata`, `@Symfony`]

##### Valid Examples :+1:

```rst
:ref:`receiving them via a worker <messenger-worker>`
```

##### Invalid Examples :-1:

```rst
:ref:`receiving them via a worker<messenger-worker>`
```

## `string_replacement`

#### Groups [`@Symfony`]

#### Checks

Pattern | Message
--- | ---
`**type**: ``int``` | Please replace "%s" with "**type**: ``integer``"
`**type**: ``bool``` | Please replace "%s" with "**type**: ``boolean``"

## `title_underline_length_must_match_title_length`

## `typo`

  > _Report common typos._

#### Groups [`@Sonata`, `@Symfony`]

#### Checks

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
`/achived/i` | Typo in word "%s"
`/overriden/i` | Typo in word "%s"
`/succesfully/i` | Typo in word "%s"
`/optionnally/i` | Typo in word "%s"
`/esimated/i` | Typo in word "%s"
`/strengh/i` | Typo in word "%s"
`/mehtod/i` | Typo in word "%s"
`/contraint/i` | Typo in word "%s"
`/instanciation/i` | Typo in word "%s", use "instantiation"

## `unused_links`

  > _Report all links which are defined, but not used in the file anymore._

#### Groups [`@Sonata`, `@Symfony`]

## `use_deprecated_directive_instead_of_versionadded`

#### Groups [`@Sonata`, `@Symfony`]

## `use_https_xsd_urls`

#### Groups [`@Sonata`, `@Symfony`]

## `use_named_constructor_without_new_keyword_rule`

  > _Ensures that named constructor is used without "new" keyword._

##### Valid Examples :+1:

```rst
new Uuid()
```

##### Invalid Examples :-1:

```rst
new Uuid::fromString()
```

## `valid_inline_highlighted_namespaces`

  > _Ensures to have 2 backslashes when highlighting a namespace to have valid output._

#### Groups [`@Sonata`, `@Symfony`]

##### Valid Examples :+1:

```rst
``App\Entity\Foo``
```

```rst
`App\\Entity\\Foo`
```

##### Invalid Examples :-1:

```rst
``App\\Entity\\Foo``
```

```rst
`App\Entity\Foo`
```

## `valid_use_statements`

#### Groups [`@Sonata`, `@Symfony`]

## `versionadded_directive_major_version`

#### Groups [`@Symfony`]

#### Configuration options

Name | Required
--- | ---
`major_version` | `true` | `int`

## `versionadded_directive_min_version`

#### Groups [`@Symfony`]

#### Configuration options

Name | Required
--- | ---
`min_version` | `true` | `string`

## `versionadded_directive_should_have_version`

  > _Ensure a versionadded directive has a version which follows SemVer._

#### Groups [`@Sonata`, `@Symfony`]

##### Valid Examples :+1:

```rst
.. versionadded:: 3.4
```

##### Invalid Examples :-1:

```rst
.. versionadded::
```

```rst
.. versionadded:: foo-bar
```

## `yaml_instead_of_yml_suffix`

  > _Make sure to only use `yaml` instead of `yml`._

#### Groups [`@Sonata`, `@Symfony`]

##### Valid Examples :+1:

```rst
.travis.yml
```

```rst
..code-block:: yaml
```

```rst
Please add this to your services.yaml file.
```

##### Invalid Examples :-1:

```rst
..code-block:: yml
```

```rst
Please add this to your services.yml file.
```

## `yarn_dev_option_at_the_end`

  > _Make sure yarn `--dev` option for `add` command is used at the end._

#### Groups [`@Sonata`, `@Symfony`]

##### Valid Examples :+1:

```rst
yarn add jquery --dev
```

##### Invalid Examples :-1:

```rst
yarn add --dev jquery
```

## `yarn_dev_option_not_at_the_end`

  > _Make sure yarn `--dev` option for `add` command is used at the end._

##### Valid Examples :+1:

```rst
yarn add --dev jquery
```

##### Invalid Examples :-1:

```rst
yarn add jquery --dev
```

