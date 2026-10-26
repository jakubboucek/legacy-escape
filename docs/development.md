# Development

## Structure and dependencies

Composer autoloads `JakubBoucek\Escape\` from `src/`. Both classes expose stateless static helpers; there is no initialization or service container.

Runtime requirements are PHP `~7.2 || ~8.0` and `ext-json`. Nette Utils is optional at runtime for HTML objects and installed as a development dependency. Development dependencies are Nette Tester (`^2.4 || ^2.5 || dev-master`), Nette Utils (`^3.1 || ^4.0`), and PHPStan (`1.12.32`).

`composer.lock` and `vendor/` are ignored. Dependency versions in a local installation can differ from those resolved in CI.

## Setup and checks

Run checks in Docker using `jakubboucek/lamp-devstack-php`. Use `8.6-rc-cli`, `8.5-cli`, `8.4-cli`, `8.3-cli`, `8.2-cli`, `8.1-legacy-cli`, `8.0-legacy-cli`, `7.4-legacy-cli`, `7.3-legacy-cli`, and `7.2-legacy-cli` for the PHP compatibility matrix.

Before testing each version, remove both `vendor/` and `composer.lock` and install dependencies inside the selected image. This resolves packages compatible with that PHP runtime. Run versions sequentially when sharing the working directory.

From the repository root, repeat with each tag:

```sh
image="jakubboucek/lamp-devstack-php:8.5-cli"
rm -rf vendor composer.lock
docker run --rm -v "$PWD:/app" -w /app "$image" composer install --no-interaction --prefer-dist
docker run --rm -v "$PWD:/app" -w /app "$image" composer run test
docker run --rm -v "$PWD:/app" -w /app "$image" composer run phpstan
```

Tests are `Tester\TestCase` classes in `tests/*Test.php`. Each file loads Composer's autoloader, calls `Environment::setup()`, and runs its test case. Data providers assert exact output; JSON attribute encoding also has an exception test.

The test script runs `tester tests -s -C`. PHPStan analyzes `src/` at level 7 using `phpstan.neon`; its exclusions cover optional Nette HTML interfaces across Nette Utils versions. Both checks use the container's PHP interpreter.

## Continuous integration

`.github/workflows/code_analysis.yaml` runs tests and PHPStan on pushes and pull requests, across its PHP version matrix and highest/lowest dependency resolutions. For lowest dependencies, only test and static-analysis failures are allowed; dependency installation failures intentionally fail the job. Refer to the workflow for the exact matrix.

`.gitattributes` excludes tests, CI configuration, and PHPStan configuration from distribution archives.
