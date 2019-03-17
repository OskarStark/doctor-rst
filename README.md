DOCtor-RST
==========

**This is a POC under development!**

Usage
-----

You can use it as a Github Action like this:

_.github/rst.workflow_
```
workflow "Test" {
  on = "push"
  resolves = ["DOCtor-RST"]
}

action "DOCtor-RST" {
  uses = "docker://oskarstark/doctor-rst"
  secrets = ["GITHUB_TOKEN"]
}
```

If your `*.rst` files are not located in root:
```diff
workflow "Test" {
  on = "push"
  resolves = ["DOCtor-RST"]
}

action "DOCtor-RST" {
  uses = "docker://oskarstark/doctor-rst"
  secrets = ["GITHUB_TOKEN"]
+  env = {
+    DOCS_DIR = "docs/"
+  }
}
```

Docker
------

A Docker-Image is built automatically and located here:
https://cloud.docker.com/u/oskarstark/repository/docker/oskarstark/doctor-rst

You can run it in any given directory like this:

`docker run --rm -it -e DOCS_DIR='/docs' -v ${PWD}:/docs  oskarstark/doctor-rst:latest`

Local usage
-----------

`bin/console check dummy/docs --group=@Symfony`

or

`bin/console check dummy/docs --group=@Sonata`

Available rules
---------------

* **be_keen_to_newcomers** [`@Sonata`, `@Symfony`]

  _Do not use beliting words!_

  Default:
    - `simply`
    - `easy`
    - `easily`
    - `obviously`
    - `trivial`

* **blank_line_after_directive** [`@Sonata`, `@Symfony`]

  _Make sure you have a blank line after each directive._

* **blank_line_after_filepath_in_code_block** [`@Sonata`]

  _Make sure you have a blank line after a filepath in a code block. This rule respects PHP, YAML, XML and Twig._

* **blank_line_after_filepath_in_php_code_block**

  _Make sure you have a blank line after a filepath in a PHP code block._

* **blank_line_after_filepath_in_twig_code_block**

  _Make sure you have a blank line after a filepath in a Twig code block._

* **blank_line_after_filepath_in_xml_code_block**

  _Make sure you have a blank line after a filepath in a XML code block._

* **blank_line_after_filepath_in_yaml_code_block**

  _Make sure you have a blank line after a filepath in a YAML code block._

* **composer_dev_option_at_the_end** [`@Sonata`]

* **composer_dev_option_not_at_the_end** [`@Symfony`]

* **ensure_order_of_code_blocks_in_configuration_block** [`@Sonata`, `@Symfony`]

* **even_brackets_count** [`@Sonata`, `@Symfony`]

* **extend_abstract_admin** [`@Sonata`]

* **extend_abstract_controller** [`@Symfony`]

* **extend_controller** [`@Symfony`]

* **final_admin_classes** [`@Sonata`]

* **final_admin_extension_classes** [`@Sonata`]

* **kernel_instead_of_app_kernel** [`@Sonata`]

* **line_length**

* **no_admin_yaml** [`@Sonata`]

* **no_app_bundle** [`@Sonata`]

* **no_app_console** [`@Sonata`, `@Symfony`]

* **no_bash_prompt** [`@Sonata`]

* **no_blank_line_after_filepath_in_code_block**

* **no_blank_line_after_filepath_in_php_code_block** [`@Symfony`]

* **no_blank_line_after_filepath_in_twig_code_block** [`@Symfony`]

* **no_blank_line_after_filepath_in_xml_code_block** [`@Symfony`]

* **no_blank_line_after_filepath_in_yaml_code_block** [`@Symfony`]

* **no_composer_phar** [`@Sonata`]

* **no_composer_req** [`@Symfony`]

* **no_config_yaml** [`@Sonata`, `@Symfony`]

* **no_explicit_use_of_code_block_php** [`@Symfony`]

* **no_inheritdoc** [`@Sonata`]

* **no_php_open_tag_in_code_block_php_directive** [`@Sonata`, `@Symfony`]

* **no_php_prefix_before_bin_console** [`@Sonata`]

* **no_php_prefix_before_composer** [`@Sonata`]

* **no_space_before_self_xml_closing_tag** [`@Sonata`]

* **not_many_blank_lines** [`@Sonata`]

* **php_open_tag_in_code_block_php_directive**

* **php_prefix_before_bin_console** [`@Symfony`]

* **replacement** [`@Sonata`, `@Symfony`]

* **short_array_syntax** [`@Sonata`]

* **space_before_self_xml_closing_tag**

* **typo** [`@Sonata`, `@Symfony`]

  Default:
    - `compsoer`
    - `registerbundles()`
    - `retun`
    - `displayes`
    - `mantains`
    - `doctine`
    - `adress`
    - `argon21`
    - `descritpion`

* **use_deprecated_directive_instead_of_versionadded** [`@Sonata`, `@Symfony`]

* **versionadded_directive_major_version** [`@Symfony`]

* **versionadded_directive_min_version** [`@Symfony`]

* **versionadded_directive_should_have_version** [`@Sonata`, `@Symfony`]

* **yaml_instead_of_yml_suffix** [`@Sonata`, `@Symfony`]

* **yarn_dev_option_at_the_end** [`@Sonata`, `@Symfony`]

* **yarn_dev_option_not_at_the_end**

Todo:
-----

* Allow to specifiy which rules should be used via config file (`.doctor-rst`)
* Allow to register custom Rules
* Move logic from Command to Services
