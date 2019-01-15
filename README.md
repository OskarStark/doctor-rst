RST-Checker
===========

**This is a POC under development!**

Usage
-----

You can use it as a Github Action like this:

_.github/rst.workflow_
```
workflow "Test" {
  on = "push"
  resolves = ["RST-Checker"]
}

action "RST-Checker" {
  uses = "docker://oskarstark/rst-checker"
  secrets = ["GITHUB_TOKEN"]
}
```

If your `*.rst` files are not located in root:
```diff
workflow "Test" {
  on = "push"
  resolves = ["RST-Checker"]
}

action "RST-Checker" {
  uses = "docker://oskarstark/rst-checker"
  secrets = ["GITHUB_TOKEN"]
+  env = {
+    DOCS_DIR = "docs/"
+  }
}
```

Docker
------

A Docker-Image is built automatically and located here:
https://cloud.docker.com/u/oskarstark/repository/docker/oskarstark/rst-checker

You can run it in any given directory like this:

`docker run --rm -it -e DOCS_DIR='/docs' -v ${PWD}:/docs  oskarstark/rst-checker:latest`

Local usage
-----------

`bin/console check dummy/docs --group=@Symfony`

or

`bin/console check dummy/docs --group=@Sonata`

Todo:
-----

* Allow to specifiy which rules should be used via config file (`.rst-checker`)
* Allow to register custom Rules
* Move logic from Command to Services
