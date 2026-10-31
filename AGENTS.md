# Project instructions

## Communication and documentation

- Write project documentation, code identifiers, comments, commit messages, and other project text in English.
- Keep documentation concise and factual, describing the current implemented state. Use commits and PR descriptions for change history, not documentation.
- Temporary plans are appropriate only for large, multi-iteration refactoring; consolidate them when the work is complete.
- Store project and code knowledge here or in linked `docs/` files. Personal memory is reserved for user preferences and feedback, never project facts.

## Project map

This is `jakubboucek/legacy-escape`, a standalone PHP library for context-specific output escaping in legacy applications. It has no application server, database, frontend build, or runtime configuration.

- [README.md](README.md): installation and usage.
- [docs/api.md](docs/api.md): public API behavior and context boundaries.
- [docs/development.md](docs/development.md): structure, dependencies, and verification.
- `src/Escape.php`: general escaping helpers.
- `src/EscapeCss.php`: CSS color filtering.
- `tests/*Test.php`: Nette Tester regression tests.

## Implementation conventions

- Preserve PHP 7.2 compatibility; `composer.json` allows `~7.2 || ~8.0`.
- Use the PSR-4 namespace `JakubBoucek\Escape` and existing four-space indentation and brace style.
- Source files intentionally omit `strict_types` for legacy compatibility. Tests use strict types. Do not apply a blanket strict-types rule to `src/`.
- Keep the public helpers static and preserve their string return values and existing coercion behavior.
- Preserve both `Nette\HtmlStringable` and `Nette\Utils\IHtmlString` support. Nette Utils is optional for consumers; the related PHPStan exclusions accommodate different installed versions.
- Escaping is context-specific. Preserve exact output, malformed UTF-8 handling, JSON errors, and trusted HTML behavior unless a behavior change is explicitly intended.
- Cover behavior changes with focused input/output cases in the existing test classes. Run tests and PHPStan in `jakubboucek/lamp-devstack-php` Docker images, not host PHP. Before testing each PHP version, delete `vendor/` and `composer.lock`, then run `composer install` in that version's container. See the [development guide](docs/development.md) for tags and commands.
- Do not edit generated dependencies in `vendor/` or commit the ignored `composer.lock`.
