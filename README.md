DOCtor-RST
==========

**This is a POC under development!**

Available rules
---------------

You can find the available rules [here](docs/rules.md).

Usage
-----

You can use it as a Github Action like this:
```yaml
# .github/workflows/lint.yaml

on: [push, pull_request]
name: Lint
jobs:
    doctor-rst:
        name: DOCtor-RST
        runs-on: ubuntu-latest
        steps:
            - uses: actions/checkout@master
            - name: DOCtor-RST
              uses: docker://oskarstark/doctor-rst
              with:
                  args: --short --error-formatter=github
```

If your `*.rst` files are not located in root:
```diff
              uses: docker://oskarstark/doctor-rst
              with:
                  args: --short --error-formatter=github
+              env:
+                  DOCS_DIR: 'docs/'
```

Error Formatter
---------------

* **detect** _(default)_ If running inside GithubActions CI environment, `github` is used, otherwise `console`.

* **github** Used to annotate your pull requests.

* **console** Used as to generate a human readable output.

To force the usage of a specific formatter, use the `--error-format` option.

Docker
------

A Docker-Image is built automatically and located here:
https://cloud.docker.com/u/oskarstark/repository/docker/oskarstark/doctor-rst

You can run it in any given directory like this:

`docker run --rm -it -e DOCS_DIR='/docs' -v ${PWD}:/docs  oskarstark/doctor-rst:latest`

Local usage
-----------

`bin/console analyze dummy/docs --group=@Symfony`

or

`bin/console analyze dummy/docs --group=@Sonata`

Todo:
-----

* Allow to register custom Rules
* Move logic from Command to Services
