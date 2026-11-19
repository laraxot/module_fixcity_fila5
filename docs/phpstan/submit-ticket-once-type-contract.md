---
title: SubmitTicketOnceAction type contract
description: Why the idempotency action uses typed constants and how PHPStan validates its runtime contract.
---

# SubmitTicketOnceAction: contratto dei tipi

`SubmitTicketOnceAction` protegge il flusso di invio ticket da doppie richieste concorrenti. Le costanti definiscono TTL della cache e durata del lock; non sono dettagli cosmetici: sono parte del contratto operativo dell'idempotenza.

PHPStan livello massimo segnalava che le costanti non avevano un tipo dichiarato. La correzione è stata tipizzare entrambe come `int`, mantenendo invariati i valori e quindi il comportamento di cache e lock.

La verifica canonica è:

```bash
cd laravel
./vendor/bin/phpstan analyse Modules
```

Non usare cast, baseline o `@phpstan-ignore`: se cambiano TTL o lock, aggiornare prima il comportamento e i test dell'azione.
