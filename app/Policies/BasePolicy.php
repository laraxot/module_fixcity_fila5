<?php

declare(strict_types=1);

namespace Modules\Fixcity\Policies;

use Modules\User\Models\Policies\UserBasePolicy;
use Modules\Xot\Contracts\UserContract;

/**
 * Base di tutte le policy del modulo Fixcity.
 *
 * Gerarchia (mai saltare un livello):
 *
 *   Modules\Xot\Models\Policies\XotBasePolicy      radice: before(), viewAny()
 *     └── Modules\User\Models\Policies\UserBasePolicy   ruoli e permessi utente
 *           └── Modules\Fixcity\Policies\BasePolicy      questo livello: default del dominio ticket
 *                 └── Modules\Fixcity\Policies\TicketPolicy
 *
 * *Perché esiste*: `UserBasePolicy` è vuota e `XotBasePolicy` non sa nulla di ticket.
 * Senza questo livello, ogni policy del modulo che ha bisogno di una eccezione
 * (ruolo che vede tutto, stato che autorizza l'assegnazione) deve duplicare la logica:
 * con N policy, N copie. Qui la logica si scrive una volta sola e le policy del modulo
 * dichiarano solo le eccezioni.
 *
 * @see docs/wiki/rules/no-controllers-rule.md  (Pattern: nessuna logica in HTTP)
 */
abstract class BasePolicy extends UserBasePolicy
{
    /**
     * Ruoli autorizzati a operare sulle segnalazioni dal lato PA.
     *
     * Il PRD definisce tre personas: cittadino, operatore/ente, supervisore/admin.
     * Solo le ultime due toccano i dati di una segnalazione.
     *
     * @var list<'operator'|'supervisor'|'admin'>
     */
    protected const array PA_ROLES = ['operator', 'supervisor', 'admin'];

    /**
     * L'utente agisce come PA (non come cittadino proprietario)?
     */
    protected function isPaOperator(UserContract $user): bool
    {
        foreach (self::PA_ROLES as $role) {
            if ($user->hasRole($role)) {
                return true;
            }
        }

        return false;
    }
}
