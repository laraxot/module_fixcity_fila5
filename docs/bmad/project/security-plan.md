---
title: "FixCity security and privacy plan"
type: security-plan
status: draft-for-owner-review
updated: 2026-10-07
tags: [bmad, security, privacy, gdpr, tenant]
qmd: "FixCity security privacy tenant owner capability code GDPR release"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/383"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/392"
---

# Security plan

## Controls

- Authenticate report creation and private tracking; authorize every action through
  policy/UserContract and tenant context.
- Treat `owner_id` as the only citizen ownership authority; never infer access from
  audit fields or sequential IDs.
- Serialize public payloads with explicit allowlists; capability codes are secrets,
  rate limited and never included for guests without authorization.
- Validate media type/size, remove unsafe metadata where policy requires, and apply
  retention/deletion rules owned by Media/Gdpr.
- Record state-change actor, reason and timestamp in Activity/Fixcity; keep internal
  notes out of public timelines.
- Keep secrets in environment/secret manager; no credentials in docs, fixtures or
  browser output.

## Privacy decisions requiring owner

The checkbox may be acknowledgement or legal consent; code must not decide. Owner
must approve purpose, legal basis, evidence, retention, DPO/contact and localized copy
before G-01 closes. Gdpr workflows must then be tested for export, deletion and audit.

## Threat verification

Run guest/owner/other-user/operator/supervisor/admin matrix, ID enumeration checks,
rate-limit checks, upload abuse checks, tenant-crossing checks and log redaction
checks on the candidate release.
