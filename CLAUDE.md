# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project overview

A Symfony 7.4 / PHP 8.5 modular monolith demonstrating **Command and Query (CQRS)** over a strict module/layer architecture. Pure JSON API; MariaDB persistence; JWT auth for mobile, Symfony security tokens for CMS. See `README.md` for full architectural rationale (in particular the "Zero CRUD, zero PUT/PATCH/DELETE" section explaining why all writes are POST commands with optimistic locking).

## Common commands

All `composer` / `bin/console` / `bin/phpunit` commands assume you are inside the `php-api` container (the local PHP install on the host is typically not the right version). Use `docker compose exec php-api <cmd>` from outside, or run directly inside.

| Task | Command |
|---|---|
| Bring up stack (DB + PHP, runs migrations + seed imports) | `docker compose up` |
| Static analysis (PHPStan) | `composer phpstan` |
| Code style fix | `composer cs-fixer` |
| Module/layer boundary check (deptrac) | `composer deptrac` |
| Regenerate `public/openapi.{json,yaml}` from DTOs | `composer openapi` |
| Full test suite | `bin/phpunit` |
| Single test file | `bin/phpunit tests/Customer/SomeTest.php` |
| Single test method | `bin/phpunit --filter testMethodName` |
| HTML coverage report into `coverage/` | `composer coverage` |

The app listens on `http://127.0.0.1:86` (host port mapped from container port 8000; override with `APP_PORT`). MariaDB is on host port `63306`.

## MCP tooling (AI Mate)

When inspecting the live app, prefer the `symfony-ai-mate` MCP tools over the equivalent CLI:

- `server-info` instead of `php -v` / `php -m` / `uname`
- `symfony-services` instead of `bin/console debug:container`
- `symfony-profiler-list` / `symfony-profiler-get` for inspecting recorded requests
- Resources `symfony-profiler://profile/{token}` and `symfony-profiler://profile/{token}/{collector}`

These read the compiled container / profiler directly and auto-redact secrets.

## Architecture

### Modular monolith — vertical slices

Each directory directly under `src/` is a **module** (`Customer`, `Offer`, `Product`, `Store`, `User`) except `Shared/`, which is common infrastructure (not a module). The PSR-4 root is `Module\` → `src/`. Tests autoload as `Tests\` → `tests/`. The `mate/` directory holds AI Mate config — do not touch unless changing tooling.

Within each module the three horizontal layers are:

```
src/<Module>/
   Access/      Controllers (HTTP) and Console commands — translate external calls into Action calls
      Console/
      Controller/
   Action/      Core domain — public surface of the module
      Command/    Mutations (POST). One class per command, one public method.
      Query/      Reads. Forbidden to mutate state.
      Dto/        Input/output DTOs (the read/write models)
      Enum/
   Model/       Doctrine entities (module-private state) and repositories
   Support/     Internal helpers, internal-only DTOs
```

Key invariants enforced by `deptrac.yaml` (`composer deptrac` runs in CI-style checks):

- **Other modules may only enter through `<Module>/Action/`** — never reach into `Model/`, `Support/`, or `Access/` of another module.
- `Access/` may use only its module's `Action/` + `Shared/` + vendor.
- `Action/` may use its own `Support/` / `Model/` + other modules' `Action/` + `Shared/` + vendor.
- `Model/` may use only its module's `Enum/` + `Shared/` + vendor.
- The DTO is the **only** way data crosses a module boundary in either direction.

### Command & Query conventions

- All mutating endpoints are `POST` and take a request DTO. There are no `PUT`/`PATCH`/`DELETE` routes.
- Entities use optimistic locking via a `version` field. Commands that mutate accept the client's last-seen `version` in the input DTO and reject on mismatch — see the "change-visibility" flow in `README.md` for the canonical pattern.
- Database table names are prefixed per module (e.g. `Offer` entity → `ofr_offer` table) so class names and table names differ on purpose.

### Auth

- **Mobile (`/api/...`)**: JWT bearing user ID and selected store ID — auth decisions can be made without a DB hit. Issued by `Customer` module.
- **CMS (`/api/admin/...`)**: Symfony security custom token, issued by `User` module.

### Tests

- `tests/IntegrationTestCase.php` boots the Symfony kernel; `tests/UnitTestCase.php` and `tests/ApplicationTestCase.php` are the other base classes.
- `dama/doctrine-test-bundle` wraps each test in a transaction (registered as a PHPUnit extension in `phpunit.dist.xml`) — DB state rolls back automatically; do not write manual teardown.
- `phpunit.dist.xml` has `failOnDeprecation`, `failOnNotice`, `failOnWarning` all true. New code must be deprecation-clean.

## Coding style

- `declare(strict_types=1);` is required in every PHP file (enforced by php-cs-fixer).
- Base ruleset is `@Symfony` with a few overrides (see `.php-cs-fixer.dist.php`): `control_structure_continuation_position` on next line, no Yoda style, single space around `.`, no blank line after `<?php`.
- Risky rules are enabled; let `composer cs-fixer` apply them rather than hand-editing style.

## Where new code goes

When adding a feature, decide first which module owns it (which bounded context the action belongs to), then:

1. If it's a new external entry point → `Access/Controller/` or `Access/Console/` in that module.
2. The actual logic goes into a single-purpose class in `Action/Command/` (mutation) or `Action/Query/` (read).
3. Input and output DTOs go in `Action/Dto/`. These DTOs are part of the public contract — they show up in the OpenAPI doc and are what other modules see.
4. Entities and repositories stay private to the module under `Model/`.
5. Run `composer deptrac` before pushing to confirm no boundary was broken.

<local-rules>
Never use `docker compose`.

For PHPUnit test prefer `createStub()` over `createMock()`.
</local-rules>
