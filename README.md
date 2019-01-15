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

Todo:
-----

* Allow to specifiy which rules should be used via config file (`.doctor-rst`)
* Allow to register custom Rules
* Move logic from Command to Services
