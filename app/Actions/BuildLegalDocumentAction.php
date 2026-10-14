<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Support\Str;
use Spatie\QueueableAction\QueueableAction;

use function Safe\preg_replace;

/**
 * Dati della pagina di un documento legale del tenant (informativa privacy, note legali, media policy; DCP-130/131): il Markdown
 * dell'ente reso in HTML sicuro e, per l'informativa, l'elenco delle sezioni dell'art. 13 non pubblicabili (AssessPrivacyPolicyAction).
 *
 * L'HTML ha le intestazioni abbassate di un livello (la pagina ha gia' il suo `<h1>`), niente HTML grezzo e niente link non sicuri.
 * Senza testo `html` e' null: la pagina mostra l'avviso, mai un 404 ne' un contenuto vuoto.
 */
final class BuildLegalDocumentAction
{
    use QueueableAction;

    /**
     * @return array{html: string|null, issues: array<string, string>}
     */
    public function execute(string $file, bool $assessPrivacy = false): array
    {
        $markdown = app(GetPublishedLegalDocumentAction::class)->execute($file);

        return [
            'html' => $markdown === null ? null : $this->render($markdown),
            'issues' => $assessPrivacy ? app(AssessPrivacyPolicyAction::class)->execute($markdown) : [],
        ];
    }

    private function render(string $markdown): string
    {
        return (string) Str::markdown(
            preg_replace('/^(#{1,5})(?=\s)/m', '#$1', $markdown),
            ['html_input' => 'strip', 'allow_unsafe_links' => false],
        );
    }
}
