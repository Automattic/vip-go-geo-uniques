# VIP Go Geo Uniques

Geo-based unique visitor tracking for the WordPress VIP Go platform.

## Project Knowledge

| Property | Value |
|----------|-------|
| **Main file** | `vip-go-geo-uniques.php` |
| **Text domain** | `vip-go-geo-uniques` |
| **Namespace** | `Automattic\VIPGoGeoUniques` (tests only) |
| **Source directory** | Root level (single-file plugin) |
| **Version** | 0.1.0 |
| **Requires PHP** | 8.2+ |

### Directory Structure

```
vip-go-geo-uniques/
├── vip-go-geo-uniques.php  # Main plugin file (all logic)
├── tests/
│   ├── Unit/               # Unit tests
│   └── Integration/        # Integration tests (wp-env)
├── .github/workflows/      # CI: cs-lint, integration
└── .phpcs.xml.dist         # PHPCS configuration
```

### Dependencies

- **Dev**: `automattic/vipwpcs`, `yoast/wp-test-utils`

## Commands

```bash
composer cs                # Check code standards (PHPCS)
composer cs-fix            # Auto-fix code standard violations
composer lint              # PHP syntax lint
composer test:unit         # Run unit tests
composer test:integration  # Run integration tests (requires wp-env)
composer test:integration-ms  # Run multisite integration tests
composer coverage          # Run tests with HTML coverage report
```

## Conventions

Follow the standards documented in `~/code/plugin-standards/` for full details. Key points:

- **Commits**: Use the `/commit` skill. Favour explaining "why" over "what".
- **PRs**: Use the `/pr` skill. Squash and merge by default.
- **Branch naming**: `feature/description`, `fix/description` from `develop`.
- **Testing**: Write integration tests for WordPress-dependent behaviour, unit tests for isolated logic. Use `Yoast\WPTestUtils\WPIntegration\TestCase` for integration, `Yoast\WPTestUtils\BrainMonkey\YoastTestCase` for unit.
- **Code style**: WordPress coding standards via PHPCS. Tabs for indentation.
- **i18n**: All user-facing strings must use the `vip-go-geo-uniques` text domain.

## Architectural Decisions

- **Single-file plugin**: All logic lives in the main PHP file. This is intentional given the plugin's focused scope — do not split into classes unless complexity genuinely warrants it.
- **VIP Go platform specific**: This plugin relies on VIP Go's geo-location infrastructure (HTTP headers set by the VIP Go edge layer). It will not work on standard WordPress installations.
- **Cache-aware design**: Geo-based content segmentation must work with VIP Go's page caching layer. The plugin uses cache segmentation techniques rather than bypassing the cache.

## Common Pitfalls

- Do not edit WordPress core files or bundled dependencies in `vendor/`.
- Run `composer cs` before committing. CI will reject code standard violations.
- Integration tests require `npx wp-env start` running first.
- **VIP Go only**: This plugin depends on HTTP headers set by the VIP Go edge/CDN layer. It cannot be tested for geo features on a standard local WordPress installation.
- Do not bypass or disable page caching to make geo features work. The correct approach is cache segmentation via VIP Go's vary headers.
- Geo-location data comes from edge infrastructure, not from the PHP application. Do not add client-side geo-location (e.g., browser Geolocation API) as a fallback — it would break caching.
- Do not add complex class hierarchies to a single-file plugin. If the plugin grows significantly, propose a restructure first.
