# wizard timeout recurrence import and infolist guard

## contesto

La pagina `http://127.0.0.1:8000/it/tests/segnalazione-crea` e' tornata in blocco dopo refactor del summary.

Il problema osservato e' stato doppio:

- timeout apparente lato browser quando il processo dev server era in stato degradato;
- `500` immediato con `Class "Modules\Fixcity\Filament\Widgets\TextEntry" not found` per import mancante.

## root cause

1. **import drift**: uso di `TextEntry::make()` e `ImageEntry::make()` senza i corrispondenti `use Filament\Infolists\Components\...`.
2. **rischio ricorsione summary**: naming entry non governato puo' riportare valutazioni `Get` autoreferenziali.
3. **assenza di gate forte**: i controlli precedenti non bloccavano esplicitamente questi due regressi.

## decisione

- mantenere il summary su Infolists (coerenza semantica), ma con chiavi `review_*`;
- rendere obbligatorio il guard script locale/composer;
- aggiungere regole statiche per import Infolists e naming anti-ricorsione.

## implementazione

- fix runtime su `CreateTicketWizardWidget`:
  - ripristino import `TextEntry` e `ImageEntry`;
  - mantenimento chiavi `review_name`, `review_type`, `review_address`, `review_content`, `review_email`, `review_images`.
- hardening guard script `laravel/bashscripts/quality/check-fixcity-wizard-guards.sh`:
  - fail se `TextEntry::make()` senza import;
  - fail se `ImageEntry::make()` senza import;
  - fail su chiavi summary non `review_*`;
  - `php -l` obbligatorio del widget.
- smoke runtime:
  - `http://127.0.0.1:8000/it/tests/segnalazione-crea` -> `200` dopo fix e cache clear.

## regola operativa

Prima di chiudere qualunque modifica al wizard:

```bash
cd laravel
composer run-script guard:fixcity-wizard
curl -I --max-time 15 http://127.0.0.1:8000/it/tests/segnalazione-crea
```

## collegamenti

- [CreateTicketWizardWidget](../CreateTicketWizardWidget.md)
- [filament wizard rules](../rules/filament-wizard-rules.md)
- [stories index](./index.md)
- [7-45 segnalazione crea render loop regression guard](../../../../../_bmad-output/implementation-artifacts/7-45-segnalazione-crea-render-loop-regression-guard.md)
