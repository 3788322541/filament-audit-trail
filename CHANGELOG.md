# Changelog

All notable changes to `zhenjun/filament-audit-trail` will be documented in this file.

## 1.2.0 - 2026-09-28

Multi-tenant & Pro UI groundwork.

- `AuditLog::team()` relationship, resolved via the new `filament-audit-trail.team_model` config key or the current panel's tenant model.
- `AuditLogResource` now declares `team` as its tenant ownership relationship, so the resource list is scoped to the current Filament tenant.
- `ListAuditLogs` renders header widgets from the new `filament-audit-trail.list_header_widgets` config key (populated by the Pro plugin).
- Hash chain is now tracked per team: the previous hash is looked up among the same team's entries, ignoring global scopes. Writes are serialized with an atomic cache lock when the store supports locks.
- Fixed a subtle chain bug where a `null` hash on the latest row could be used as the previous hash.

## 1.1.0 - 2026-09-26

Pre-embed Pro-tier extension points. Zero behaviour change for existing users.

- New `AuditHasher`, `TeamResolver` and `FieldPolicy` contracts under `Zhenjun\AuditTrail\Contracts`.
- Null / pass-through default implementations bound in the container; the Pro package rebinds them to real implementations without touching call sites.
- `Auditor::record()` now consults the three contracts before writing, populating `audit_logs.team_id` and `audit_logs.hash` when the Pro tier enables them.
- New `pro.*` config section (`hashing.enabled` / `hashing.secret`, `team.resolver`, `fields.whitelist`, `fields.mask`). Left at defaults in the free tier, so behaviour is unchanged.
- Hash chain query only runs when `pro.hashing.enabled` is true — no cost on the free write path.

## 1.0.0 - 2026-09-26

Initial release for Filament v5.

- Zero-config recording of `created`, `updated`, `deleted` and `restored` model events via the `Auditable` trait.
- Field-level diffs (old/new) serialized for enums, dates and JSON, stored as JSON on an append-only `audit_logs` table.
- Actor resolution across Filament panel user, Laravel guard and explicit override.
- Request context capture (IP, user agent, URL) with per-key toggles.
- Sensitive attributes excluded by default; per-model exclusion via `getAuditExcludeAttributes()`.
- Read-only Filament resource with an event filter, sortable columns and a diff detail view.
- `AuditLogRelationManager` to surface a record's own history.
- `AuditContext::silent()` / `for()` / `tag()` runtime controls and the `AuditLogged` event.
- `audit:purge` command with configurable retention.
