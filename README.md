DOCtor-RST
==========

**This is a POC under development!**

Available rules
---------------

You can find the available rules [here](docs/rules.md).

Usage
-----

You can use it as a Github Action like this:

_.github/workflows/lint.yaml_
```
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
+              env:
+                  DOCS_DIR: 'docs/'
```

Error formatter
---------------
DOCtor-RST has two error formatters `console` *(default)* and `github`. 

`github` is useful in the context of GitHub actions and can be activated with the option `--error-formatter=github`.

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
