---
title: "FixCity notifications retry after commit"
type: story
status: done
created: 2026-09-27
updated: 2026-09-27
tags: [bmad, fixcity, notifications, queue, reliability]
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/383"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/392"
related:
  - ../workflows/actor-system.md
  - STORY-003-system-notification.md
---

# STORY — Retry consegna notifiche FixCity

## Problema

Le notifiche di assegnazione e cambio stato erano sincrone rispetto al driver
predefinito e non dichiaravano retry/backoff. Un errore transitorio non aveva un
percorso automatico di riconsegna; gli observer potevano anche accodare prima che
la transazione di activity fosse conclusa.

## Criteri di accettazione

- [x] Le due notifiche implementano `ShouldQueueAfterCommit`.
- [x] Ogni job ha al massimo 3 tentativi, con backoff 60 e 300 secondi.
- [x] Testa la configurazione effettiva del `SendQueuedNotifications` Laravel:
      tentativi, backoff e dispatch-after-commit.
- [x] I payload esistenti, i destinatari e la preferenza email restano invariati.

## Limiti operativi

La consegna asincrona richiede un queue worker attivo nell'ambiente. Il job
fallito oltre i tentativi segue il backend `failed_jobs` configurato dal progetto;
una dashboard di delivery, receipt del provider, push e smoke SMTP restano fuori
da questa story.

## Verifiche

- Pest retry + assignment + status notification: 9 test / 31 asserzioni.
- Suite FixCity completa: 380 test / 1.634 asserzioni.
- PHPStan su tutti i moduli: `[OK] No errors`.
- Pint mirato superato.
- `verify-llm-wiki.sh` e `git diff --check` superati.
- Database SQLite effimero per test creato in `database/tmp/` e rimosso dopo i gate.
