<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Spatie\QueueableAction\QueueableAction;

/**
 * Informativa privacy del tenant attivo (`policy.md`). Resta il punto d'ingresso del wizard (consenso) e della registrazione:
 * "pubblicata" qui significa solo "il file esiste e non e' il modello iniziale". Se sia completa (art. 13 GDPR) lo dice
 * AssessPrivacyPolicyAction.
 */
class GetPublishedPrivacyPolicyAction
{
    use QueueableAction;

    public function execute(): ?string
    {
        return app(GetPublishedLegalDocumentAction::class)->execute('policy.md');
    }
}
