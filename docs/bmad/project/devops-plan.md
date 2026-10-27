---
title: "FixCity DevOps and operations plan"
type: devops-plan
status: draft-for-ops-review
updated: 2026-10-07
tags: [bmad, devops, queues, backup, monitoring, staging]
qmd: "FixCity staging queue worker SMTP failed jobs backup restore monitoring runbook"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/383"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/392"
---

# Operations plan

## Candidate pipeline

Build immutable candidate → install module-owned dependencies → migrate forward-only
schema → seed approved demo/tenant data → run PHPStan/Pest/Pint/view/browser checks
→ publish evidence bundle → owner go/no-go.

## Runtime services

Laravel web, database, queue worker, scheduler, cache, object storage, SMTP/provider
connectors and log/health monitoring. Notify and Job must expose retry/failure state;
no ticket creation may depend on synchronous provider success.

## Backup and recovery

Daily encrypted database backup plus tested restore, media retention/backup and a
documented RPO/RTO approved by Ops. Restore must run in an isolated database and
include verification of ticket ownership, activities and tenant configuration.

## Monitoring and incident response

Alert on queue depth/failed jobs, notification failure rate, database/storage health,
5xx/rate-limit anomalies and unauthorized-access signals. Runbooks must name owner,
rollback-safe forward fix, replay/idempotency action and evidence location.
