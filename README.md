# s2Member Modernization Lab

A focused WordPress/PHP modernization project inspired by the open-source **s2Member Framework**.

The goal is not to claim authorship of s2Member or to redistribute its commercial edition. This repository demonstrates how a legacy membership/access-control codebase can be progressively modernized while preserving its domain concepts.

## Why this project

Long-lived WordPress plugins often accumulate:

- global state and tightly-coupled classes;
- capability checks spread across many code paths;
- difficult-to-test procedural logic;
- weak separation between HTTP, domain and infrastructure concerns;
- compatibility work that becomes risky over time.

This repository isolates one representative area - **membership capability checks and access decisions** - and rewrites it with a small, testable service layer.

## Upstream reference

Domain concepts are based on the GPL-licensed s2Member Framework maintained by WP Sharks:

- upstream repository: `wpsharks/s2member`
- representative legacy areas reviewed:
  - `src/includes/classes/user-access.inc.php`
  - `src/includes/classes/pages.inc.php`
  - `src/includes/classes/posts.inc.php`
  - capability names such as `access_s2member_level1`

No s2Member Pro code is included.

## What has been modernized

- PSR-4 namespaced PHP classes
- dependency injection for capability checks
- immutable access-decision object
- validation of membership levels and custom capabilities
- REST endpoint with explicit permission handling
- audit logging abstraction
- unit tests without booting WordPress
- PHPCS / PSR-12
- PHPStan static analysis
- GitHub Actions CI

## Example

Legacy-style access checks are often embedded directly in request handling:

```php
if ( current_user_can( 'access_s2member_level1' ) ) {
    // render protected content
}
```

The modernized version separates the decision:

```php
$decision = $gate->decide(1, ['premium_reports']);

if ($decision->allowed()) {
    // continue
}
```

That service can now be tested independently from WordPress.

## Requirements

- PHP 7.4+
- WordPress 6.x
- Composer for development tooling

## Development

```bash
composer install
composer test
composer analyse
composer phpcs
```

## Portfolio note

This repository is intentionally narrow. It demonstrates the approach I use when entering an existing PHP/WordPress codebase: understand current behaviour first, extract a stable seam, add tests, then refactor incrementally instead of rewriting the whole system.

## License

GPL-2.0-or-later. See [LICENSE.md](LICENSE.md).

s2Member and its original source code remain copyright their respective authors.
