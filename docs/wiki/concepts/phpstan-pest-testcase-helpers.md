---
title: "Fixcity — helper TestCase per PHPStan + Pest"
type: concept
tags: [fixcity, phpstan, pest, testing, testcase]
created: 2026-06-13
updated: 2026-06-13
qmd: "Fixcity TestCase ticket authUser workflow notification PHPStan Pest closure nullable"
issues:
  - "https://github.com/laraxot/module_fixcity_fila5/issues/52"
discussions:
  - "https://github.com/laraxot/module_fixcity_fila5/discussions/53"
related:
  - ./testing.md
  - ./phpstan-compliance.md
  - ../overviews/completion-roadmap.md
  - ../../../Xot/docs/wiki/concepts/phpstan-pest-bridge-discipline.md
---

# Fixcity — helper TestCase per PHPStan + Pest

## Perché

Dopo la migrazione `Tests/` → `tests/` (base [#370](https://github.com/laraxot/base_fixcity_fila5/issues/370)), PHPStan segnalava **88 errori** solo in `Modules/Fixcity/tests/`. Causa: proprietà nullable su `Modules\Fixcity\Tests\TestCase` (`$ticket`, `$user`, `$workflowService`, …) passate direttamente a metodi che richiedono tipi non-null. Pest non propaga il narrowing di `Assert::assertNotNull` nelle closure.

## Pattern SSoT (religione)

### 1. Helper non-null sul TestCase

```php
public function ticket(): Ticket
{
    Assert::assertNotNull($this->ticket);
    return $this->ticket;
}
```

Stesso schema per: `authUser()`, `authAdmin()`, `workflow()`, `ticketService()`, `notification()`.

**Nei test:** passare sempre `$this->ticket()`, mai `$this->ticket` come argomento.

### 2. Action test — variabile locale typed

```php
$action = new ChangeStatus;
$action->execute($ticket, 'resolved', '…');
```

Evita `$this->action` dinamico non dichiarato su TestCase.

### 3. Batch action — espressione inline

```php
(new GenerateTicketsAction())->execute($count);
```

### 4. Mock — `mockService()` da XotBaseTestCase

```php
$this->mockService(NotificationService::class, function (mixed $mock) use ($ticket): void {
    // …
});
```

Mai `$this->mock()` protetto nelle closure Pest.

### 5. Helper file `tests/helpers/PestHelper.php`

Funzioni globali opzionali (`safe_instance`, `assert_non_null`). Devono rispettare PHPDoc:

- `@return class-string` → ritornare la stringa classe, non concatenare `'::class'`
- `@return void` su funzioni che lanciano eccezione, non `bool`

Non referenziate nei test al 2026-06-13 — tenere tipizzate per PHPStan se il file resta nello scan.

### 6. Dati sacri

- `DatabaseTransactions` sul TestCase Fixcity
- **Vietato** `RefreshDatabase` nei test Fixcity

## File toccati (sessione 2026-06-13)

| Area | File |
|------|------|
| TestCase | `tests/TestCase.php` |
| Services | `tests/Unit/Services/NotificationServiceTest.php`, `WorkflowServiceTest.php`, `TicketServiceTest.php` |
| Actions | `tests/Unit/Actions/ChangeStatusTest.php`, `GenerateTicketsActionTest.php`, `GetTicketSlaMetricsActionTest.php` |
| Feature | `tests/Feature/TicketWorkflowIntegrationTest.php` |
| Widget | `tests/Unit/CreateTicketWizardWidgetViewTest.php` |

## Verifica

```bash
cd laravel
./vendor/bin/phpstan analyse Modules/Fixcity
./vendor/bin/pest Modules/Fixcity/tests
```

`phpstan.neon` — solo utente.

## Debito architetturale collegato

I test su `Modules\Fixcity\Services\*` coprono classi legacy. Roadmap: spostare logica su `Actions/*` ([no-services-rule](../../rules/no-services-rule.md)) e riscrivere i test verso le Action owner.
