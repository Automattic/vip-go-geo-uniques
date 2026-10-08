# Contributing to VIP Go Geo Uniques

Thank you for your interest in contributing. This document covers setting up a local environment, running the checks, and proposing a change.

## Code of Conduct

This project follows the [Automattic Code of Conduct](https://automattic.com/code-of-conduct/).

## Development Setup

### Prerequisites

- [Node.js](https://nodejs.org/) LTS or later (for `npx wp-env`)
- [Docker Desktop](https://www.docker.com/products/docker-desktop)
- [Composer](https://getcomposer.org/)

### Setup

1. Clone the repository.
2. Install dependencies:
   ```bash
   composer install
   ```
3. Start the WordPress environment:
   ```bash
   npx wp-env start
   ```
4. Visit http://localhost:8888 and log in with `admin` / `password`.

Geo-targeting relies on the `$_SERVER['GEOIP_COUNTRY_CODE']` value that the VIP Platform sets, so it won't behave as it does in production locally. The tests set it where they need it.

### Checks

```bash
composer lint                # PHP syntax
composer cs                  # Coding standards
composer test:unit           # Unit tests (no WordPress needed)
composer test:integration    # Integration tests (single site, needs wp-env)
composer test:integration-ms # Integration tests (multisite, needs wp-env)
```

## Workflow

1. Create a branch from `develop`:
   ```bash
   git checkout develop
   git pull origin develop
   git checkout -b fix/short-description
   ```
2. Make your change, with tests.
3. Run the checks locally.
4. Push your branch and open a pull request against `develop`.

## Code Standards

We follow the [WordPress VIP Coding Standards](https://github.com/Automattic/VIP-Coding-Standards), configured in `.phpcs.xml.dist`. Run `composer cs-fix` to fix what PHPCS can fix automatically.

## Tests

Unit tests live in `tests/Unit/` and integration tests in `tests/Integration/`.

- New behaviour should come with tests.
- A bug fix should come with a test that fails without the fix.
- Name tests after the behaviour they check, for example `test_add_location_registers_location()`.

## Pull Requests

- Keep each pull request to one change, and explain what it does and why.
- Reference related issues, for example `Fixes #123`.
- Make sure CI passes before requesting a review.
- **Sign your commits.** `develop` and `main` only accept commits with a verified signature, so a pull request with an unsigned commit cannot be merged until it is re-signed and force-pushed. See GitHub's guide to [signing commits](https://docs.github.com/en/authentication/managing-commit-signature-verification/signing-commits).
