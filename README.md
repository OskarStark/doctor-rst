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
on: push
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
                  args: --short
```

If your `*.rst` files are not located in root:
```diff
on: push
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
                  args: --short
+              env:
+                  DOCS_DIR: 'docs/'
```

Docker
------

A Docker-Image is built automatically and located here:
https://cloud.docker.com/u/oskarstark/repository/docker/oskarstark/doctor-rst

You can run it in any given directory like this:

`docker run --rm -it -e DOCS_DIR='/docs' -v ${PWD}:/docs  oskarstark/doctor-rst:latest`

Local usage
-----------

`bin/console analyse dummy/docs --group=@Symfony`

or

`bin/console analyse dummy/docs --group=@Sonata`

Todo:
-----

* Allow to register custom Rules
* Move logic from Command to Services
