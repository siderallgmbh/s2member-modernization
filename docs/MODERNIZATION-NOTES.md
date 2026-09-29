# Modernization notes

## Baseline behaviour

The upstream s2Member Framework models membership levels as WordPress capabilities such as:

- `access_s2member_level0`
- `access_s2member_level1`
- ...
- `access_s2member_level4`

Custom capabilities use the `access_s2member_ccap_*` convention.

In the legacy codebase those checks appear in multiple content-access paths (posts, pages, categories, tags and URI restrictions).

## Refactoring strategy

This lab deliberately avoids a full rewrite.

### 1. Extract one decision boundary

`CapabilityGate` owns the construction and evaluation of required capabilities.

The checker itself is injected, so the class does not require WordPress during unit tests.

### 2. Return a decision object

Instead of mixing redirects, rendering and capability checks, the service returns `AccessDecision`.

HTTP code can then decide whether to return 200, 403, redirect or render alternate content.

### 3. Keep WordPress at the edge

`Infrastructure\\Plugin` is the composition root.

The only production dependency passed to the domain service is a callable around `current_user_can()`.

### 4. Add observability

`AuditLogger` demonstrates a small structured log useful when debugging real membership sites.

## Next possible steps

- dedicated repository for access-rule configuration;
- migration layer reading existing s2Member settings;
- contract tests against a WordPress test environment;
- deprecation adapters for legacy static calls;
- cache membership decisions per request;
- benchmark legacy vs extracted access paths.

## Attribution

The behavioural reference is the GPL-licensed s2Member Framework maintained by WP Sharks. This repository contains a new implementation used as a technical portfolio exercise; it is not the official s2Member project.
