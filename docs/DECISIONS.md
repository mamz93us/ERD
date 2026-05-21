# Architectural Decisions Log

Every non-trivial architectural choice is recorded here. Add a new entry at the **top** of the list (most recent first). Format:

```
## NNN — YYYY-MM-DD — Decision title
**Context**: why this came up
**Decision**: what was chosen
**Alternatives considered**: what else was on the table
**Consequences**: what this means going forward
```

---

## 003 — 2026-05-20 — DB-backed editable translations over file-only

**Context**: Original spec used `mcamara/laravel-localization` with file-based `lang/ar/*.php` + `lang/en/*.php`. Owner wants to edit bilingual UI copy at runtime through admin (e.g. correcting a label, retitling a button) without a redeploy.

**Decision**: Adopt a `translations` DB table (`group`, `key`, `text_ar`, `text_en`, `is_system`, `updated_by_user_id`) loaded via `spatie/laravel-translation-loader`. File-based strings stay in `lang/ar/`+`lang/en/` as fallback only. A one-time seeder will import file strings into DB on first boot with `is_system=true`. Filament admin resource (Phase 1) provides edit, audit log, and "reset to default" for system rows.

**Alternatives considered**:
- File-only (original spec): rejected — requires redeploy for any copy change.
- File + admin file editor: rejected — deployment friction (write permissions on `lang/`, no audit log, no per-user attribution).
- Hybrid (system in files, user-content in DB): rejected — too many cases where shipped strings need retitling by the owner. Cleaner to put everything in DB and treat files as the fallback safety net.

**Consequences**:
- New table in ERD (`translations` — added to `docs/SCHEMA.md` §5.1).
- New package: `spatie/laravel-translation-loader`.
- Mandatory audit logging on `translations.text_ar`/`text_en` (added to the cross-cutting audit list in CLAUDE.md §10).
- Phase 1 deliverable expands: must include the translation loader wiring + Filament resource.
- Phase 13 hardening must verify the loader resolves DB before files (test asserts override behavior).
- Filament v4 patterns also resolve labels via `__()`, so they pick up DB translations automatically.

---

## 002 — 2026-05-20 — Filament v5 over v4 (and over v3)

**Context**: Original spec was written around Filament v3. Owner asked for v4 at project kickoff (May 2026), believing v4 was the current stable. When composer installed Filament during Phase 0, it picked v5.6.4 — v4.0.0 had been released with a security advisory (`PKSA-yb9k-ykqx-p2zw`) that was never patched in the 4.x line; the maintainers rolled the fix forward into v5.0+, which became the supported current line. Composer's `block-insecure` audit refused to install v4.0.0, leaving v5 as the only safe option.

**Decision**: Build the admin UI on Filament v5.6. Skip v4 entirely. Translate any v3-pattern code examples in the spec to their v5 equivalents as we encounter them.

**Alternatives considered**:
- Force-install Filament v4.0.0 with `--ignore-platform-reqs` or by disabling `block-insecure`: rejected — accepting a known security advisory on the admin framework that processes financial data is unacceptable.
- Filament v3.3 (the original fallback documented in the roadmap): rejected — v3 still works on Laravel 12 but v5 is the actively maintained line. No reason to start a greenfield project on a deprecated major version.
- Hand-build the admin without Filament: rejected as before — would lose months of CRUD work.

**Consequences**:
- All Filament-related deliverables in CLAUDE.md now mean v5 wherever they previously said v3 or v4. Spec already patched.
- v3-syntax code examples in the original spec must be translated to v5 (v5 has further evolved schema-builder + form-builder APIs from both v3 and v4). Each non-trivial pattern translation gets its own DECISIONS.md entry.
- Plugin audit during Phase 0: confirm critical community plugins have v5 ports — especially calendar/Gantt for Phase 5 and Inertia bridges for Phase 11. If any critical plugin lacks a v5 port, file a separate DECISIONS.md entry and either find an alternative plugin, build a custom Filament v5 page, or (last resort) fall back to a maintained v3.x line for that specific module.
- Owner approved v4 specifically; we shipped v5 instead. Owner notified in Phase 0 sign-off summary so they know to ask for v4 *only if* they have a specific reason — otherwise v5 is strictly better.

---

## 001 — 2026-05-20 — Laravel 12 over Laravel 11

**Context**: Original spec was written for Laravel 11. Owner directed at project kickoff to build on Laravel 12 instead.

**Decision**: Build on Laravel 12.x. PHP requirement remains 8.2+ (satisfied by XAMPP's PHP 8.2.12 local and cPanel's selectable PHP).

**Alternatives considered**:
- Stay on Laravel 11: rejected by owner directive. L11 still supported but L12 is current stable.

**Consequences**:
- All packages chosen in Phase 0 must support Laravel 12. Filament v4, spatie/permission v6.25+, spatie/laravel-medialibrary v11.22+, etc.
- cPanel/AlmaLinux deployment unchanged — L12 has the same PHP-FPM + MySQL/MariaDB story as L11.
- spec §3 stack table updated in `ERD/CLAUDE.md`.

---

*End of decisions log. Add new entries above this line.*
