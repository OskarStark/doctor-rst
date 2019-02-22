workflow "Test" {
  on = "push"
  resolves = [
    "PHP-CS-Fixer",
    "PHPStan"
  ]
}

action "PHP-CS-Fixer" {
  uses = "docker://oskarstark/php-cs-fixer-ga"
  args = "--diff --dry-run"
}

action "PHPStan" {
  uses = "docker://oskarstark/phpstan-ga"
  args = "analyse src --level=6"
}
