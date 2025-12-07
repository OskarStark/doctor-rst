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
* [ensure_attribute_between_backticks_in_content](#ensure_attribute_between_backticks_in_content)
* [ensure_bash_prompt_before_composer_command](#ensure_bash_prompt_before_composer_command)
* [ensure_class_constant](#ensure_class_constant)
* [ensure_correct_format_for_phpfunction](#ensure_correct_format_for_phpfunction)
* [ensure_exactly_one_space_before_directive_type](#ensure_exactly_one_space_before_directive_type)
* [ensure_exactly_one_space_between_link_definition_and_link](#ensure_exactly_one_space_between_link_definition_and_link)
* [ensure_explicit_nullable_types](#ensure_explicit_nullable_types)
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
* [filepath_and_namespace_should_match](#filepath_and_namespace_should_match)
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
* [no_attribute_redundant_parenthesis](#no_attribute_redundant_parenthesis)
* [no_bash_prompt](#no_bash_prompt)
* [no_blank_line_after_filepath_in_code_block](#no_blank_line_after_filepath_in_code_block)
* [no_blank_line_after_filepath_in_php_code_block](#no_blank_line_after_filepath_in_php_code_block)
* [no_blank_line_after_filepath_in_twig_code_block](#no_blank_line_after_filepath_in_twig_code_block)
* [no_blank_line_after_filepath_in_xml_code_block](#no_blank_line_after_filepath_in_xml_code_block)
* [no_blank_line_after_filepath_in_yaml_code_block](#no_blank_line_after_filepath_in_yaml_code_block)
* [no_brackets_in_method_directive](#no_brackets_in_method_directive)
* [no_broken_ref_directive](#no_broken_ref_directive)
* [no_composer_phar](#no_composer_phar)
* [no_composer_req](#no_composer_req)
* [no_config_yaml](#no_config_yaml)
* [no_contraction](#no_contraction)
* [no_directive_after_shorthand](#no_directive_after_shorthand)
* [no_duplicate_use_statements](#no_duplicate_use_statements)
* [no_empty_directive](#no_empty_directive)
* [no_empty_literals](#no_empty_literals)
* [no_explicit_use_of_code_block_php](#no_explicit_use_of_code_block_php)
* [no_footnotes](#no_footnotes)
* [no_inheritdoc_in_code_examples](#no_inheritdoc_in_code_examples)
* [no_merge_conflict](#no_merge_conflict)
* [no_namespace_after_use_statements](#no_namespace_after_use_statements)
* [no_non_breaking_space](#no_non_breaking_space)
* [no_php_open_tag_in_code_block_php_directive](#no_php_open_tag_in_code_block_php_directive)
* [no_php_prefix_before_bin_console](#no_php_prefix_before_bin_console)
* [no_php_prefix_before_composer](#no_php_prefix_before_composer)
* [no_space_before_self_xml_closing_tag](#no_space_before_self_xml_closing_tag)
* [no_typographic_quotes](#no_typographic_quotes)
* [non_static_phpunit_assertions](#non_static_phpunit_assertions)
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
* [use_double_backticks_for_inline_literals](#use_double_backticks_for_inline_literals)
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

#### References

- Rule class: [App\Rule\AmericanEnglish](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/AmericanEnglish.php)
- Test class: [App\Tests\Rule\AmericanEnglishTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/AmericanEnglishTest.php)

## `argument_variable_must_match_type`

  > _Make sure argument variable name match for type_

#### Configuration options

Name | Required | Allowed Types | Default
--- | --- | --- | ---
`arguments` | `false` | `array` | `[]`

#### References

- Rule class: [App\Rule\ArgumentVariableMustMatchType](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/ArgumentVariableMustMatchType.php)
- Test class: [App\Tests\Rule\ArgumentVariableMustMatchTypeTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/ArgumentVariableMustMatchTypeTest.php)

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

#### References

- Rule class: [App\Rule\AvoidRepetetiveWords](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/AvoidRepetetiveWords.php)
- Test class: [App\Tests\Rule\AvoidRepetetiveWordsTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/AvoidRepetetiveWordsTest.php)

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

#### References

- Rule class: [App\Rule\BeKindToNewcomers](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/BeKindToNewcomers.php)

## `blank_line_after_anchor`

  > _Make sure you have a blank line after anchor (`.. anchor:`)._

#### Groups [`@Sonata`, `@Symfony`]

#### References

- Rule class: [App\Rule\BlankLineAfterAnchor](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/BlankLineAfterAnchor.php)
- Test class: [App\Tests\Rule\BlankLineAfterAnchorTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/BlankLineAfterAnchorTest.php)

## `blank_line_after_colon`

  > _Make sure you have a blank line after a sentence which ends with a colon (`:`)._

#### Groups [`@Sonata`, `@Symfony`]

#### References

- Rule class: [App\Rule\BlankLineAfterColon](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/BlankLineAfterColon.php)
- Test class: [App\Tests\Rule\BlankLineAfterColonTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/BlankLineAfterColonTest.php)

## `blank_line_after_directive`

  > _Make sure you have a blank line after each directive._

#### Groups [`@Sonata`, `@Symfony`]

#### References

- Rule class: [App\Rule\BlankLineAfterDirective](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/BlankLineAfterDirective.php)
- Test class: [App\Tests\Rule\BlankLineAfterDirectiveTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/BlankLineAfterDirectiveTest.php)

## `blank_line_after_filepath_in_code_block`

  > _Make sure you have a blank line after a filepath in a code block. This rule respects PHP, YAML, XML and Twig._

#### Groups [`@Sonata`, `@Symfony`]

#### References

- Rule class: [App\Rule\BlankLineAfterFilepathInCodeBlock](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/BlankLineAfterFilepathInCodeBlock.php)
- Test class: [App\Tests\Rule\BlankLineAfterFilepathInCodeBlockTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/BlankLineAfterFilepathInCodeBlockTest.php)

## `blank_line_after_filepath_in_php_code_block`

  > _Make sure you have a blank line after a filepath in a PHP code block._

#### References

- Rule class: [App\Rule\BlankLineAfterFilepathInPhpCodeBlock](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/BlankLineAfterFilepathInPhpCodeBlock.php)
- Test class: [App\Tests\Rule\BlankLineAfterFilepathInPhpCodeBlockTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/BlankLineAfterFilepathInPhpCodeBlockTest.php)

## `blank_line_after_filepath_in_twig_code_block`

  > _Make sure you have a blank line after a filepath in a Twig code block._

#### References

- Rule class: [App\Rule\BlankLineAfterFilepathInTwigCodeBlock](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/BlankLineAfterFilepathInTwigCodeBlock.php)
- Test class: [App\Tests\Rule\BlankLineAfterFilepathInTwigCodeBlockTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/BlankLineAfterFilepathInTwigCodeBlockTest.php)

## `blank_line_after_filepath_in_xml_code_block`

  > _Make sure you have a blank line after a filepath in a XML code block._

#### References

- Rule class: [App\Rule\BlankLineAfterFilepathInXmlCodeBlock](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/BlankLineAfterFilepathInXmlCodeBlock.php)
- Test class: [App\Tests\Rule\BlankLineAfterFilepathInXmlCodeBlockTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/BlankLineAfterFilepathInXmlCodeBlockTest.php)

## `blank_line_after_filepath_in_yaml_code_block`

  > _Make sure you have a blank line after a filepath in a YAML code block._

#### References

- Rule class: [App\Rule\BlankLineAfterFilepathInYamlCodeBlock](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/BlankLineAfterFilepathInYamlCodeBlock.php)
- Test class: [App\Tests\Rule\BlankLineAfterFilepathInYamlCodeBlockTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/BlankLineAfterFilepathInYamlCodeBlockTest.php)

## `blank_line_before_directive`

  > _Make sure you have a blank line before each directive._

#### Groups [`@Sonata`, `@Symfony`]

#### References

- Rule class: [App\Rule\BlankLineBeforeDirective](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/BlankLineBeforeDirective.php)
- Test class: [App\Tests\Rule\BlankLineBeforeDirectiveTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/BlankLineBeforeDirectiveTest.php)

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

#### References

- Rule class: [App\Rule\ComposerDevOptionAtTheEnd](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/ComposerDevOptionAtTheEnd.php)
- Test class: [App\Tests\Rule\ComposerDevOptionAtTheEndTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/ComposerDevOptionAtTheEndTest.php)

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

#### References

- Rule class: [App\Rule\ComposerDevOptionNotAtTheEnd](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/ComposerDevOptionNotAtTheEnd.php)
- Test class: [App\Tests\Rule\ComposerDevOptionNotAtTheEndTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/ComposerDevOptionNotAtTheEndTest.php)

## `correct_code_block_directive_based_on_the_content`

#### Groups [`@Sonata`, `@Symfony`]

#### References

- Rule class: [App\Rule\CorrectCodeBlockDirectiveBasedOnTheContent](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/CorrectCodeBlockDirectiveBasedOnTheContent.php)

## `deprecated_directive_major_version`

#### Groups [`@Symfony`]

#### Configuration options

Name | Required
--- | ---
`major_version` | `true` | `int`

#### References

- Rule class: [App\Rule\DeprecatedDirectiveMajorVersion](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/DeprecatedDirectiveMajorVersion.php)
- Test class: [App\Tests\Rule\DeprecatedDirectiveMajorVersionTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/DeprecatedDirectiveMajorVersionTest.php)

## `deprecated_directive_min_version`

#### Groups [`@Symfony`]

#### Configuration options

Name | Required
--- | ---
`min_version` | `true` | `string`

#### References

- Rule class: [App\Rule\DeprecatedDirectiveMinVersion](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/DeprecatedDirectiveMinVersion.php)
- Test class: [App\Tests\Rule\DeprecatedDirectiveMinVersionTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/DeprecatedDirectiveMinVersionTest.php)

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

#### References

- Rule class: [App\Rule\DeprecatedDirectiveShouldHaveVersion](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/DeprecatedDirectiveShouldHaveVersion.php)
- Test class: [App\Tests\Rule\DeprecatedDirectiveShouldHaveVersionTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/DeprecatedDirectiveShouldHaveVersionTest.php)

## `ensure_attribute_between_backticks_in_content`

  > _Make sure to use backticks around attributes in content_

##### Valid Examples :+1:

```rst
Use ``#[Route]`` to define route
```

##### Invalid Examples :-1:

```rst
Use #[Route] to define route
```

#### References

- Rule class: [App\Rule\EnsureAttributeBetweenBackticksInContent](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/EnsureAttributeBetweenBackticksInContent.php)
- Test class: [App\Tests\Rule\EnsureAttributeBetweenBackticksInContentTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/EnsureAttributeBetweenBackticksInContentTest.php)

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

#### References

- Rule class: [App\Rule\EnsureBashPromptBeforeComposerCommand](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/EnsureBashPromptBeforeComposerCommand.php)
- Test class: [App\Tests\Rule\EnsureBashPromptBeforeComposerCommandTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/EnsureBashPromptBeforeComposerCommandTest.php)

## `ensure_class_constant`

  > _Make sure to use ::class over get_class_

##### Valid Examples :+1:

```rst
MyClass::class
```

##### Invalid Examples :-1:

```rst
get_class(new MyClass())
```

#### References

- Rule class: [App\Rule\EnsureClassConstant](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/EnsureClassConstant.php)
- Test class: [App\Tests\Rule\EnsureClassConstantTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/EnsureClassConstantTest.php)

## `ensure_correct_format_for_phpfunction`

  > _Ensure phpfunction directive do not end with ()._

##### Valid Examples :+1:

```rst
:phpfunction:`mb_detect_encoding`.
```

##### Invalid Examples :-1:

```rst
:phpfunction:`mb_detect_encoding()`.
```

#### References

- Rule class: [App\Rule\EnsureCorrectFormatForPhpfunction](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/EnsureCorrectFormatForPhpfunction.php)
- Test class: [App\Tests\Rule\EnsureCorrectFormatForPhpfunctionTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/EnsureCorrectFormatForPhpfunctionTest.php)

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

#### References

- Rule class: [App\Rule\EnsureExactlyOneSpaceBeforeDirectiveType](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/EnsureExactlyOneSpaceBeforeDirectiveType.php)
- Test class: [App\Tests\Rule\EnsureExactlyOneSpaceBeforeDirectiveTypeTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/EnsureExactlyOneSpaceBeforeDirectiveTypeTest.php)

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

#### References

- Rule class: [App\Rule\EnsureExactlyOneSpaceBetweenLinkDefinitionAndLink](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/EnsureExactlyOneSpaceBetweenLinkDefinitionAndLink.php)
- Test class: [App\Tests\Rule\EnsureExactlyOneSpaceBetweenLinkDefinitionAndLinkTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/EnsureExactlyOneSpaceBetweenLinkDefinitionAndLinkTest.php)

## `ensure_explicit_nullable_types`

  > _Ensure explicit nullable types in method arguments._

#### Groups [`@Symfony`]

##### Valid Examples :+1:

```rst
function foo(?string $bar = null)
```

```rst
function foo(string|null $bar = null)
```

##### Invalid Examples :-1:

```rst
function foo(string $bar = null)
```

#### References

- Rule class: [App\Rule\EnsureExplicitNullableTypes](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/EnsureExplicitNullableTypes.php)
- Test class: [App\Tests\Rule\EnsureExplicitNullableTypesTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/EnsureExplicitNullableTypesTest.php)

## `ensure_github_directive_start_with_prefix`

#### Configuration options

Name | Required
--- | ---
`prefix` | `true` | `string`

#### References

- Rule class: [App\Rule\EnsureGithubDirectiveStartWithPrefix](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/EnsureGithubDirectiveStartWithPrefix.php)
- Test class: [App\Tests\Rule\EnsureGithubDirectiveStartWithPrefixTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/EnsureGithubDirectiveStartWithPrefixTest.php)

## `ensure_link_bottom`

  > _Ensure link lines are at the bottom of the file._

#### Groups [`@Symfony`]

#### References

- Rule class: [App\Rule\EnsureLinkBottom](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/EnsureLinkBottom.php)
- Test class: [App\Tests\Rule\EnsureLinkBottomTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/EnsureLinkBottomTest.php)

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

#### References

- Rule class: [App\Rule\EnsureLinkDefinitionContainsValidUrl](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/EnsureLinkDefinitionContainsValidUrl.php)
- Test class: [App\Tests\Rule\EnsureLinkDefinitionContainsValidUrlTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/EnsureLinkDefinitionContainsValidUrlTest.php)

## `ensure_order_of_code_blocks_in_configuration_block`

#### Groups [`@Sonata`, `@Symfony`]

#### References

- Rule class: [App\Rule\EnsureOrderOfCodeBlocksInConfigurationBlock](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/EnsureOrderOfCodeBlocksInConfigurationBlock.php)
- Test class: [App\Tests\Rule\EnsureOrderOfCodeBlocksInConfigurationBlockTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/EnsureOrderOfCodeBlocksInConfigurationBlockTest.php)

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

```rst
The :class:`Symfony\\AI\\Platform\PlatformInterface` class
```

#### References

- Rule class: [App\Rule\EnsurePhpReferenceSyntax](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/EnsurePhpReferenceSyntax.php)
- Test class: [App\Tests\Rule\EnsurePhpReferenceSyntaxTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/EnsurePhpReferenceSyntaxTest.php)

## `extend_abstract_admin`

  > _Ensure `AbstractAdmin` and the corresponding namespace `Sonata\AdminBundle\Admin\AbstractAdmin` is used._

#### Groups [`@Sonata`]

#### References

- Rule class: [App\Rule\ExtendAbstractAdmin](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/ExtendAbstractAdmin.php)
- Test class: [App\Tests\Rule\ExtendAbstractAdminTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/ExtendAbstractAdminTest.php)

## `extend_abstract_controller`

  > _Ensure `AbstractController` and the corresponding namespace `Symfony\Bundle\FrameworkBundle\Controller\AbstractController` is used. Instead of `Symfony\Bundle\FrameworkBundle\Controller\Controller`._

#### Groups [`@Symfony`]

#### References

- Rule class: [App\Rule\ExtendAbstractController](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/ExtendAbstractController.php)
- Test class: [App\Tests\Rule\ExtendAbstractControllerTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/ExtendAbstractControllerTest.php)

## `extend_controller`

  > _Ensure `Controller` and the corresponding namespace `Symfony\Bundle\FrameworkBundle\Controller\Controller` is used. Instead of `Symfony\Bundle\FrameworkBundle\Controller\AbstractController`._

#### Groups [`@Symfony`]

#### References

- Rule class: [App\Rule\ExtendController](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/ExtendController.php)
- Test class: [App\Tests\Rule\ExtendControllerTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/ExtendControllerTest.php)

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

#### References

- Rule class: [App\Rule\ExtensionXlfInsteadOfXliff](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/ExtensionXlfInsteadOfXliff.php)
- Test class: [App\Tests\Rule\ExtensionXlfInsteadOfXliffTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/ExtensionXlfInsteadOfXliffTest.php)

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

#### References

- Rule class: [App\Rule\FilenameUsesDashesOnly](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/FilenameUsesDashesOnly.php)
- Test class: [App\Tests\Rule\FilenameUsesDashesOnlyTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/FilenameUsesDashesOnlyTest.php)

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

#### References

- Rule class: [App\Rule\FilenameUsesUnderscoresOnly](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/FilenameUsesUnderscoresOnly.php)
- Test class: [App\Tests\Rule\FilenameUsesUnderscoresOnlyTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/FilenameUsesUnderscoresOnlyTest.php)

## `filepath_and_namespace_should_match`

  > _Ensures the namespace in a PHP code block matches the filepath._

#### Groups [`@Sonata`, `@Symfony`]

#### Configuration options

Name | Required
--- | ---
`namespace_mapping` | `true` | `array`
`ignored_paths` | `false` | `array`
`ignored_namespaces` | `false` | `array`

##### Valid Examples :+1:

```rst
.. code-block:: php

    // src/Acme/FooBundle/Entity/User.php
    namespace Acme\FooBundle\Entity;
```

##### Invalid Examples :-1:

```rst
.. code-block:: php

    // src/Acme/FooBundle/Entity/User.php
    namespace Acme\WrongBundle\Entity;
```

#### References

- Rule class: [App\Rule\FilepathAndNamespaceShouldMatch](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/FilepathAndNamespaceShouldMatch.php)
- Test class: [App\Tests\Rule\FilepathAndNamespaceShouldMatchTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/FilepathAndNamespaceShouldMatchTest.php)

## `final_admin_classes`

#### Groups [`@Sonata`]

#### References

- Rule class: [App\Rule\FinalAdminClasses](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/FinalAdminClasses.php)
- Test class: [App\Tests\Rule\FinalAdminClassesTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/FinalAdminClassesTest.php)

## `final_admin_extension_classes`

#### Groups [`@Sonata`]

#### References

- Rule class: [App\Rule\FinalAdminExtensionClasses](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/FinalAdminExtensionClasses.php)
- Test class: [App\Tests\Rule\FinalAdminExtensionClassesTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/FinalAdminExtensionClassesTest.php)

## `forbidden_directives`

  > _Make sure forbidden directives are not used_

#### Groups [`@Symfony`]

#### Configuration options

Name | Required | Allowed Types | Default
--- | --- | --- | ---
`directives` | `false` | `array` | `[]`

#### References

- Rule class: [App\Rule\ForbiddenDirectives](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/ForbiddenDirectives.php)
- Test class: [App\Tests\Rule\ForbiddenDirectivesTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/ForbiddenDirectivesTest.php)

## `indention`

#### Groups [`@Experimental`]

#### Configuration options

Name | Required | Allowed Types | Default
--- | --- | --- | ---
`size` | `false` | `int` | `4`

#### References

- Rule class: [App\Rule\Indention](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/Indention.php)
- Test class: [App\Tests\Rule\IndentionTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/IndentionTest.php)

## `kernel_instead_of_app_kernel`

#### Groups [`@Sonata`]

#### References

- Rule class: [App\Rule\KernelInsteadOfAppKernel](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/KernelInsteadOfAppKernel.php)
- Test class: [App\Tests\Rule\KernelInsteadOfAppKernelTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/KernelInsteadOfAppKernelTest.php)

## `line_length`

#### Configuration options

Name | Required | Allowed Types | Default
--- | --- | --- | ---
`max` | `false` | `int` | `80`

#### References

- Rule class: [App\Rule\LineLength](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/LineLength.php)
- Test class: [App\Tests\Rule\LineLengthTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/LineLengthTest.php)

## `lowercase_as_in_use_statements`

#### Groups [`@Sonata`, `@Symfony`]

#### References

- Rule class: [App\Rule\LowercaseAsInUseStatements](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/LowercaseAsInUseStatements.php)

## `max_blank_lines`

#### Groups [`@Sonata`, `@Symfony`]

#### Configuration options

Name | Required | Allowed Types | Default
--- | --- | --- | ---
`max` | `false` | `int` | `2`

#### References

- Rule class: [App\Rule\MaxBlankLines](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/MaxBlankLines.php)
- Test class: [App\Tests\Rule\MaxBlankLinesTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/MaxBlankLinesTest.php)

## `max_colons`

  > _Make sure you have max 2 colons (`::`)._

#### Groups [`@Sonata`, `@Symfony`]

##### Valid Examples :+1:

```rst
temp::
```

##### Invalid Examples :-1:

```rst
temp:::
```

#### References

- Rule class: [App\Rule\MaxColons](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/MaxColons.php)
- Test class: [App\Tests\Rule\MaxColonsTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/MaxColonsTest.php)

## `no_admin_yaml`

#### Groups [`@Sonata`]

#### References

- Rule class: [App\Rule\NoAdminYaml](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/NoAdminYaml.php)
- Test class: [App\Tests\Rule\NoAdminYamlTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/NoAdminYamlTest.php)

## `no_app_bundle`

#### Groups [`@Sonata`]

#### References

- Rule class: [App\Rule\NoAppBundle](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/NoAppBundle.php)

## `no_app_console`

#### Groups [`@Sonata`, `@Symfony`]

#### References

- Rule class: [App\Rule\NoAppConsole](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/NoAppConsole.php)

## `no_attribute_redundant_parenthesis`

  > _Make sure there is no redundant parenthesis on attribute_

#### Groups [`@Symfony`]

##### Valid Examples :+1:

```rst
#[Bar]
```

```rst
#[Bar('foo')]
```

##### Invalid Examples :-1:

```rst
#[Bar()]
```

#### References

- Rule class: [App\Rule\NoAttributeRedundantParenthesis](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/NoAttributeRedundantParenthesis.php)
- Test class: [App\Tests\Rule\NoAttributeRedundantParenthesisTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/NoAttributeRedundantParenthesisTest.php)

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

#### References

- Rule class: [App\Rule\NoBashPrompt](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/NoBashPrompt.php)
- Test class: [App\Tests\Rule\NoBashPromptTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/NoBashPromptTest.php)

## `no_blank_line_after_filepath_in_code_block`

#### References

- Rule class: [App\Rule\NoBlankLineAfterFilepathInCodeBlock](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/NoBlankLineAfterFilepathInCodeBlock.php)
- Test class: [App\Tests\Rule\NoBlankLineAfterFilepathInCodeBlockTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/NoBlankLineAfterFilepathInCodeBlockTest.php)

## `no_blank_line_after_filepath_in_php_code_block`

#### Groups [`@Symfony`]

#### References

- Rule class: [App\Rule\NoBlankLineAfterFilepathInPhpCodeBlock](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/NoBlankLineAfterFilepathInPhpCodeBlock.php)
- Test class: [App\Tests\Rule\NoBlankLineAfterFilepathInPhpCodeBlockTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/NoBlankLineAfterFilepathInPhpCodeBlockTest.php)

## `no_blank_line_after_filepath_in_twig_code_block`

#### Groups [`@Symfony`]

#### References

- Rule class: [App\Rule\NoBlankLineAfterFilepathInTwigCodeBlock](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/NoBlankLineAfterFilepathInTwigCodeBlock.php)
- Test class: [App\Tests\Rule\NoBlankLineAfterFilepathInTwigCodeBlockTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/NoBlankLineAfterFilepathInTwigCodeBlockTest.php)

## `no_blank_line_after_filepath_in_xml_code_block`

#### Groups [`@Symfony`]

#### References

- Rule class: [App\Rule\NoBlankLineAfterFilepathInXmlCodeBlock](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/NoBlankLineAfterFilepathInXmlCodeBlock.php)
- Test class: [App\Tests\Rule\NoBlankLineAfterFilepathInXmlCodeBlockTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/NoBlankLineAfterFilepathInXmlCodeBlockTest.php)

## `no_blank_line_after_filepath_in_yaml_code_block`

#### Groups [`@Symfony`]

#### References

- Rule class: [App\Rule\NoBlankLineAfterFilepathInYamlCodeBlock](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/NoBlankLineAfterFilepathInYamlCodeBlock.php)
- Test class: [App\Tests\Rule\NoBlankLineAfterFilepathInYamlCodeBlockTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/NoBlankLineAfterFilepathInYamlCodeBlockTest.php)

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

#### References

- Rule class: [App\Rule\NoBracketsInMethodDirective](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/NoBracketsInMethodDirective.php)
- Test class: [App\Tests\Rule\NoBracketsInMethodDirectiveTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/NoBracketsInMethodDirectiveTest.php)

## `no_broken_ref_directive`

  > _Ensure only valid :ref: directives._

#### Groups [`@Symfony`]

##### Valid Examples :+1:

```rst
See this :ref:`Foo`
```

##### Invalid Examples :-1:

```rst
See this ref:`Foo`
```

#### References

- Rule class: [App\Rule\NoBrokenRefDirective](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/NoBrokenRefDirective.php)
- Test class: [App\Tests\Rule\NoBrokenRefDirectiveTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/NoBrokenRefDirectiveTest.php)

## `no_composer_phar`

#### Groups [`@Sonata`]

#### References

- Rule class: [App\Rule\NoComposerPhar](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/NoComposerPhar.php)

## `no_composer_req`

#### Groups [`@Symfony`]

#### References

- Rule class: [App\Rule\NoComposerReq](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/NoComposerReq.php)
- Test class: [App\Tests\Rule\NoComposerReqTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/NoComposerReqTest.php)

## `no_config_yaml`

#### Groups [`@Sonata`, `@Symfony`]

#### References

- Rule class: [App\Rule\NoConfigYaml](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/NoConfigYaml.php)

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

#### References

- Rule class: [App\Rule\NoContraction](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/NoContraction.php)
- Test class: [App\Tests\Rule\NoContractionTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/NoContractionTest.php)

## `no_directive_after_shorthand`

  > _Ensure that no directive follows the shorthand `::`. This could lead to broken markup._

#### Groups [`@Sonata`, `@Symfony`]

#### References

- Rule class: [App\Rule\NoDirectiveAfterShorthand](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/NoDirectiveAfterShorthand.php)
- Test class: [App\Tests\Rule\NoDirectiveAfterShorthandTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/NoDirectiveAfterShorthandTest.php)

## `no_duplicate_use_statements`

  > _Ensure there is not same use statement twice_

#### Groups [`@Symfony`]

#### References

- Rule class: [App\Rule\NoDuplicateUseStatements](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/NoDuplicateUseStatements.php)
- Test class: [App\Tests\Rule\NoDuplicateUseStatementsTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/NoDuplicateUseStatementsTest.php)

## `no_empty_directive`

  > _Ensure a directive is not empty._

#### Groups [`@Sonata`, `@Symfony`]

##### Valid Examples :+1:

```rst
.. note::

    This is a note.
```

##### Invalid Examples :-1:

```rst
.. note::

This is a note.
```

#### References

- Rule class: [App\Rule\NoEmptyDirective](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/NoEmptyDirective.php)
- Test class: [App\Tests\Rule\NoEmptyDirectiveTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/NoEmptyDirectiveTest.php)

## `no_empty_literals`

  > _Make sure that no empty literals are used._

#### Groups [`@Sonata`, `@Symfony`]

##### Valid Examples :+1:

```rst
Please use ``foo``...
```

##### Invalid Examples :-1:

```rst
Please use ````...
```

#### References

- Rule class: [App\Rule\NoEmptyLiterals](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/NoEmptyLiterals.php)
- Test class: [App\Tests\Rule\NoEmptyLiteralsTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/NoEmptyLiteralsTest.php)

## `no_explicit_use_of_code_block_php`

#### Groups [`@Symfony`]

#### References

- Rule class: [App\Rule\NoExplicitUseOfCodeBlockPhp](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/NoExplicitUseOfCodeBlockPhp.php)
- Test class: [App\Tests\Rule\NoExplicitUseOfCodeBlockPhpTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/NoExplicitUseOfCodeBlockPhpTest.php)

## `no_footnotes`

  > _Make sure there is no footnotes_

#### Groups [`@Symfony`]

##### Invalid Examples :-1:

```rst
.. [5] A numerical footnote. Note
```

#### References

- Rule class: [App\Rule\NoFootnotes](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/NoFootnotes.php)
- Test class: [App\Tests\Rule\NoFootnotesTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/NoFootnotesTest.php)

## `no_inheritdoc_in_code_examples`

#### Groups [`@Sonata`, `@Symfony`]

#### References

- Rule class: [App\Rule\NoInheritdocInCodeExamples](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/NoInheritdocInCodeExamples.php)
- Test class: [App\Tests\Rule\NoInheritdocInCodeExamplesTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/NoInheritdocInCodeExamplesTest.php)

## `no_merge_conflict`

  > _Ensure that the files does not contain merge conflicts._

#### Groups [`@Symfony`]

#### References

- Rule class: [App\Rule\NoMergeConflict](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/NoMergeConflict.php)
- Test class: [App\Tests\Rule\NoMergeConflictTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/NoMergeConflictTest.php)

## `no_namespace_after_use_statements`

#### Groups [`@Sonata`, `@Symfony`]

#### References

- Rule class: [App\Rule\NoNamespaceAfterUseStatements](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/NoNamespaceAfterUseStatements.php)
- Test class: [App\Tests\Rule\NoNamespaceAfterUseStatementsTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/NoNamespaceAfterUseStatementsTest.php)

## `no_non_breaking_space`

  > _Ensure no non-breaking spaces or other invisible whitespace characters are used._

#### Groups [`@Symfony`]

##### Valid Examples :+1:

```rst
Valid sentence
```

##### Invalid Examples :-1:

```rst
Invalid sentence
```

#### References

- Rule class: [App\Rule\NoNonBreakingSpace](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/NoNonBreakingSpace.php)
- Test class: [App\Tests\Rule\NoNonBreakingSpaceTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/NoNonBreakingSpaceTest.php)

## `no_php_open_tag_in_code_block_php_directive`

#### Groups [`@Sonata`, `@Symfony`]

#### References

- Rule class: [App\Rule\NoPhpOpenTagInCodeBlockPhpDirective](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/NoPhpOpenTagInCodeBlockPhpDirective.php)
- Test class: [App\Tests\Rule\NoPhpOpenTagInCodeBlockPhpDirectiveTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/NoPhpOpenTagInCodeBlockPhpDirectiveTest.php)

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

#### References

- Rule class: [App\Rule\NoPhpPrefixBeforeBinConsole](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/NoPhpPrefixBeforeBinConsole.php)
- Test class: [App\Tests\Rule\NoPhpPrefixBeforeBinConsoleTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/NoPhpPrefixBeforeBinConsoleTest.php)

## `no_php_prefix_before_composer`

#### Groups [`@Sonata`]

#### References

- Rule class: [App\Rule\NoPhpPrefixBeforeComposer](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/NoPhpPrefixBeforeComposer.php)
- Test class: [App\Tests\Rule\NoPhpPrefixBeforeComposerTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/NoPhpPrefixBeforeComposerTest.php)

## `no_space_before_self_xml_closing_tag`

#### Groups [`@Sonata`]

#### References

- Rule class: [App\Rule\NoSpaceBeforeSelfXmlClosingTag](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/NoSpaceBeforeSelfXmlClosingTag.php)
- Test class: [App\Tests\Rule\NoSpaceBeforeSelfXmlClosingTagTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/NoSpaceBeforeSelfXmlClosingTagTest.php)

## `no_typographic_quotes`

  > _Do not use typographic quotes._

#### Groups [`@Symfony`]

##### Valid Examples :+1:

```rst
Lorem 'ipsum' dolor "sit amet"
```

##### Invalid Examples :-1:

```rst
Lorem ‘ipsum’ dolor “sit amet”
```

#### References

- Rule class: [App\Rule\NoTypographicQuotes](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/NoTypographicQuotes.php)
- Test class: [App\Tests\Rule\NoTypographicQuotesTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/NoTypographicQuotesTest.php)

## `non_static_phpunit_assertions`

  > _Use `$this->assert*` over static calls._

#### Groups [`@Symfony`]

##### Valid Examples :+1:

```rst
$this->assertTrue($foo);
```

##### Invalid Examples :-1:

```rst
self::assertTrue($foo);
```

#### References

- Rule class: [App\Rule\NonStaticPhpunitAssertions](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/NonStaticPhpunitAssertions.php)
- Test class: [App\Tests\Rule\NonStaticPhpunitAssertionsTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/NonStaticPhpunitAssertionsTest.php)

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

#### References

- Rule class: [App\Rule\OnlyBackslashesInNamespaceInPhpCodeBlock](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/OnlyBackslashesInNamespaceInPhpCodeBlock.php)
- Test class: [App\Tests\Rule\OnlyBackslashesInNamespaceInPhpCodeBlockTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/OnlyBackslashesInNamespaceInPhpCodeBlockTest.php)

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

#### References

- Rule class: [App\Rule\OnlyBackslashesInUseStatementsInPhpCodeBlock](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/OnlyBackslashesInUseStatementsInPhpCodeBlock.php)
- Test class: [App\Tests\Rule\OnlyBackslashesInUseStatementsInPhpCodeBlockTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/OnlyBackslashesInUseStatementsInPhpCodeBlockTest.php)

## `ordered_use_statements`

#### Groups [`@Sonata`, `@Symfony`]

#### References

- Rule class: [App\Rule\OrderedUseStatements](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/OrderedUseStatements.php)
- Test class: [App\Tests\Rule\OrderedUseStatementsTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/OrderedUseStatementsTest.php)

## `php_open_tag_in_code_block_php_directive`

#### References

- Rule class: [App\Rule\PhpOpenTagInCodeBlockPhpDirective](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/PhpOpenTagInCodeBlockPhpDirective.php)
- Test class: [App\Tests\Rule\PhpOpenTagInCodeBlockPhpDirectiveTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/PhpOpenTagInCodeBlockPhpDirectiveTest.php)

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

#### References

- Rule class: [App\Rule\PhpPrefixBeforeBinConsole](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/PhpPrefixBeforeBinConsole.php)
- Test class: [App\Tests\Rule\PhpPrefixBeforeBinConsoleTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/PhpPrefixBeforeBinConsoleTest.php)

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

#### References

- Rule class: [App\Rule\RemoveTrailingWhitespace](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/RemoveTrailingWhitespace.php)
- Test class: [App\Tests\Rule\RemoveTrailingWhitespaceTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/RemoveTrailingWhitespaceTest.php)

## `replace_code_block_types`

  > _Propose alternatives for disallowed code block types._

#### Groups [`@Sonata`, `@Symfony`]

#### Checks

Pattern | Message
--- | ---
`jinja` | Please do not use type "jinja" for code-block, use "twig" instead
`html+jinja` | Please do not use type "html+jinja" for code-block, use "html+twig" instead
`js` | Please do not use type "js" for code-block, use "javascript" instead

#### References

- Rule class: [App\Rule\ReplaceCodeBlockTypes](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/ReplaceCodeBlockTypes.php)

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
`/\beg\./` | Please replace "%s" with "e.g."

#### References

- Rule class: [App\Rule\Replacement](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/Replacement.php)
- Test class: [App\Tests\Rule\ReplacementTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/ReplacementTest.php)

## `short_array_syntax`

#### Groups [`@Sonata`]

#### References

- Rule class: [App\Rule\ShortArraySyntax](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/ShortArraySyntax.php)
- Test class: [App\Tests\Rule\ShortArraySyntaxTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/ShortArraySyntaxTest.php)

## `space_before_self_xml_closing_tag`

#### References

- Rule class: [App\Rule\SpaceBeforeSelfXmlClosingTag](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/SpaceBeforeSelfXmlClosingTag.php)
- Test class: [App\Tests\Rule\SpaceBeforeSelfXmlClosingTagTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/SpaceBeforeSelfXmlClosingTagTest.php)

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

#### References

- Rule class: [App\Rule\SpaceBetweenLabelAndLinkInDoc](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/SpaceBetweenLabelAndLinkInDoc.php)
- Test class: [App\Tests\Rule\SpaceBetweenLabelAndLinkInDocTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/SpaceBetweenLabelAndLinkInDocTest.php)

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

#### References

- Rule class: [App\Rule\SpaceBetweenLabelAndLinkInRef](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/SpaceBetweenLabelAndLinkInRef.php)
- Test class: [App\Tests\Rule\SpaceBetweenLabelAndLinkInRefTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/SpaceBetweenLabelAndLinkInRefTest.php)

## `string_replacement`

#### Groups [`@Symfony`]

#### Checks

Pattern | Message
--- | ---
`**type**: ``int``` | Please replace "%s" with "**type**: ``integer``"
`**type**: ``bool``` | Please replace "%s" with "**type**: ``boolean``"

#### References

- Rule class: [App\Rule\StringReplacement](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/StringReplacement.php)
- Test class: [App\Tests\Rule\StringReplacementTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/StringReplacementTest.php)

## `title_underline_length_must_match_title_length`

#### References

- Rule class: [App\Rule\TitleUnderlineLengthMustMatchTitleLength](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/TitleUnderlineLengthMustMatchTitleLength.php)
- Test class: [App\Tests\Rule\TitleUnderlineLengthMustMatchTitleLengthTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/TitleUnderlineLengthMustMatchTitleLengthTest.php)

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

#### References

- Rule class: [App\Rule\Typo](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/Typo.php)
- Test class: [App\Tests\Rule\TypoTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/TypoTest.php)

## `unused_links`

  > _Report all links which are defined, but not used in the file anymore._

#### Groups [`@Sonata`, `@Symfony`]

#### References

- Rule class: [App\Rule\UnusedLinks](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/UnusedLinks.php)
- Test class: [App\Tests\Rule\UnusedLinksTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/UnusedLinksTest.php)

## `use_deprecated_directive_instead_of_versionadded`

#### Groups [`@Sonata`, `@Symfony`]

#### References

- Rule class: [App\Rule\UseDeprecatedDirectiveInsteadOfVersionadded](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/UseDeprecatedDirectiveInsteadOfVersionadded.php)
- Test class: [App\Tests\Rule\UseDeprecatedDirectiveInsteadOfVersionaddedTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/UseDeprecatedDirectiveInsteadOfVersionaddedTest.php)

## `use_double_backticks_for_inline_literals`

  > _Ensure double backticks are used for inline literals instead of single backticks._

#### Groups [`@Symfony`]

##### Valid Examples :+1:

```rst
Please use ``vector`` for this.
```

```rst
See :ref:`my-reference` for details.
```

##### Invalid Examples :-1:

```rst
Please use `vector` for this.
```

#### References

- Rule class: [App\Rule\UseDoubleBackticksForInlineLiterals](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/UseDoubleBackticksForInlineLiterals.php)
- Test class: [App\Tests\Rule\UseDoubleBackticksForInlineLiteralsTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/UseDoubleBackticksForInlineLiteralsTest.php)

## `use_https_xsd_urls`

#### Groups [`@Sonata`, `@Symfony`]

#### References

- Rule class: [App\Rule\UseHttpsXsdUrls](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/UseHttpsXsdUrls.php)
- Test class: [App\Tests\Rule\UseHttpsXsdUrlsTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/UseHttpsXsdUrlsTest.php)

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

#### References

- Rule class: [App\Rule\UseNamedConstructorWithoutNewKeywordRule](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/UseNamedConstructorWithoutNewKeywordRule.php)
- Test class: [App\Tests\Rule\UseNamedConstructorWithoutNewKeywordRuleTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/UseNamedConstructorWithoutNewKeywordRuleTest.php)

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

#### References

- Rule class: [App\Rule\ValidInlineHighlightedNamespaces](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/ValidInlineHighlightedNamespaces.php)
- Test class: [App\Tests\Rule\ValidInlineHighlightedNamespacesTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/ValidInlineHighlightedNamespacesTest.php)

## `valid_use_statements`

#### Groups [`@Sonata`, `@Symfony`]

#### References

- Rule class: [App\Rule\ValidUseStatements](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/ValidUseStatements.php)

## `versionadded_directive_major_version`

#### Groups [`@Symfony`]

#### Configuration options

Name | Required
--- | ---
`major_version` | `true` | `int`

#### References

- Rule class: [App\Rule\VersionaddedDirectiveMajorVersion](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/VersionaddedDirectiveMajorVersion.php)
- Test class: [App\Tests\Rule\VersionaddedDirectiveMajorVersionTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/VersionaddedDirectiveMajorVersionTest.php)

## `versionadded_directive_min_version`

#### Groups [`@Symfony`]

#### Configuration options

Name | Required
--- | ---
`min_version` | `true` | `string`

#### References

- Rule class: [App\Rule\VersionaddedDirectiveMinVersion](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/VersionaddedDirectiveMinVersion.php)
- Test class: [App\Tests\Rule\VersionaddedDirectiveMinVersionTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/VersionaddedDirectiveMinVersionTest.php)

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

#### References

- Rule class: [App\Rule\VersionaddedDirectiveShouldHaveVersion](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/VersionaddedDirectiveShouldHaveVersion.php)
- Test class: [App\Tests\Rule\VersionaddedDirectiveShouldHaveVersionTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/VersionaddedDirectiveShouldHaveVersionTest.php)

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

#### References

- Rule class: [App\Rule\YamlInsteadOfYmlSuffix](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/YamlInsteadOfYmlSuffix.php)
- Test class: [App\Tests\Rule\YamlInsteadOfYmlSuffixTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/YamlInsteadOfYmlSuffixTest.php)

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

#### References

- Rule class: [App\Rule\YarnDevOptionAtTheEnd](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/YarnDevOptionAtTheEnd.php)
- Test class: [App\Tests\Rule\YarnDevOptionAtTheEndTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/YarnDevOptionAtTheEndTest.php)

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

#### References

- Rule class: [App\Rule\YarnDevOptionNotAtTheEnd](https://github.com/OskarStark/doctor-rst/blob/develop/src/Rule/YarnDevOptionNotAtTheEnd.php)
- Test class: [App\Tests\Rule\YarnDevOptionNotAtTheEndTest](https://github.com/OskarStark/doctor-rst/blob/develop/tests/Rule/YarnDevOptionNotAtTheEndTest.php)

