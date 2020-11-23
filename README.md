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
name: Lint

on:
    push:
    pull_request:

jobs:
    doctor-rst:
        name: DOCtor-RST
        runs-on: ubuntu-latest
        steps:
            - name: "Checkout code"
              uses: actions/checkout@v2

            - name: DOCtor-RST
              uses: docker://oskarstark/doctor-rst
              with:
                  args: --short --error-format=github
```

If your `*.rst` files are not located in root:
```diff
              uses: docker://oskarstark/doctor-rst
              with:
                  args: --short --error-format=github
+              env:
+                  DOCS_DIR: 'docs/'
```

Error Formatter
---------------

* **detect** _(default)_ If running inside GithubActions CI environment, `github` is used, otherwise `console`.

* **github** Used to annotate your pull requests.

* **console** Used as to generate a human readable output.

To force the usage of a specific formatter, use the `--error-format` option.

Use Caching to Speedup your GithubActions builds
----------------------------------

```diff
        steps:
            - name: "Checkout"
              uses: actions/checkout@v2

+            - name: "Create cache dir"
+              run: mkdir .cache
+
+            - name: "Extract base branch name"
+              run: echo "##[set-output name=branch;]$(echo ${GITHUB_BASE_REF:=${GITHUB_REF##*/}})"
+              id: extract_base_branch
+
+            - name: "Cache DOCtor-RST"
+              uses: actions/cache@v2
+              with:
+                  path: .cache
+                  key: doctor-rst-${{ runner.os }}-${{ steps.extract_base_branch.outputs.branch }}
+                  restore-keys: |
+                      doctor-rst-${{ runner.os }}-
+                      doctor-rst-   
+
            - name: "Run DOCtor-RST"
              uses: docker://oskarstark/doctor-rst
              with:
-                 args: --short --error-format=github
+                 args: --short --error-format=github --cache-file=/github/workspace/.cache/doctor-rst.cache
```

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
