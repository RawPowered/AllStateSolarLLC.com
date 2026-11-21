# AllState Solar LLC — Thasit installation source recovery

**Status (2026-11-20): source recovery required; NOT a deployable application repository yet.**

This repository was created for `AllStateSolarLLC.com` but was not populated while AllState-specific Thasit features were developed and delivered as FTP ZIP overlays for Turbify hosting. This README initializes source-control provenance without replacing the actual installed code.

## Known deployment topology

- Turbify account home / `allstate-app/`: private Thasit application root, including `backend/`, `suite/` and the private application configuration.
- Turbify account home / `public_html/`: domain webroot bridge, assets and rewrite rules.
- PHP 8.3; MariaDB 10.11 was referenced by the v18.16B installation instructions (live current version should be reverified).
- Previous changes were distributed as versioned guarded FTP/SQL packages, including v18.16B Deal-to-Project association and v18.19A Zoho CRM mapping. Package publication and live application are different states.

## Recovery contract

1. **Do not install a fresh Thasit snapshot over the live AllState site.** Preserve the existing application, project tracking, Traditional/Messenger forum views, staff roles, customer portals, database records, Zoho connection and theme.
2. Take independently restorable backups of the existing application, `public_html`, DB and private storage, outside the webroot.
3. Identify the actually installed release/version, DB migration history, PHP version, committed source hash (if available), and the current GitHub Updates configuration. Record applied v18.x installer receipts; version numbers in prior package names are NOT proof of deployment.
4. Produce a sanitized **source snapshot** from the live installation into a private staging area, excluding credentials, OAuth tokens, encryption keys, `config.php`, `.env`, private storage, customer files, uploads, sessions, logs, DB dumps and backups. Retain a full private backup separately.
5. Compare every live source path to the newest matching installer ZIP and the integrated Thasit upstream branch. Preserve AllState-specific additions and resolve conflicts individually; never force a directory mirror or remove files absent from an archive.
6. After secret scanning and review, commit the reconciled code under a recovery branch. Make this site's own immutable release manifest and GitHub Updates package identity.
7. Qualify Forum ACP PR #22 / upstream Thasit PR #42 only **after** project/Messenger/forum dependencies and live target fingerprints match. Test route access, project updates, Deal-to-Project mapping, Traditional/Messenger switching, real-time replies, composer/drafts, permissions, and staff/customer portal isolation.
8. Once verified, publish a full site-specific update release, with a matching restorable backup, recovery plan, and updater validation. Never use Top Solar's full-site ZIP as an AllState overlay.

## Related upstream work

- Source forums implementation: `RawPowered/topsolar.energy` PR #22
- Integrated platform feature-preserving backport: `RawPowered/PowerPortals-Pressless` PR #42, based on `feat/thasit-core-company-capabilities-20261118`
- This new repository's initial README is **not** equivalent to imported live source or an applied patch.

## Release gates

All gates default to **BLOCKED** until supported by evidence: installed source inventory, secrets review, dependency comparison, tests, SQL/migration compatibility, theme check, project/Messenger regression, package signature/hash verification, backup restore test, and actual GitHub Updates install result.

No runtime files are included in this initial repository commit; no live server is changed.
