# Changelog

All notable changes to `zhenjun/filament-audit-trail` will be documented in this file.

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
