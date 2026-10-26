# Idempotent ticket submission types

`SubmitTicketOnceAction` protects the ticket wizard from duplicate requests by
using typed cache-lock and result-key constants. The constants are part of the
action's invariant, not configuration supplied by a caller, so explicit `int`
types document that invariant and satisfy strict PHPStan coverage without
changing the lock, cache or ticket-ownership behavior.

Verification is the repository command:

```bash
cd laravel && ./vendor/bin/phpstan analyse Modules --no-progress
```
