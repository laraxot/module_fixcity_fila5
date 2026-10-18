# Fix: ComponentNotFoundException ticket_list (BMAD + Second Brain)

## Analisi (BMAD)
- Errore: Livewire\Exceptions\ComponentNotFoundException - volt-anonymous-fragment.
- Causa: file `agid.blade.php` rinominato in `ticket_list.blade.php`; componenti `Agid.php` e `TicketList.php` puntavano ancora al vecchio path.
- Second Brain: registrato in MEMORY.md (iniziativa kp-174d15a58ff94901b2b0df0d86b8907a).

## Azioni svolte
1. Identificato che `agid.blade.php` era stato rinominato in `ticket_list.blade.php`.
2. Aggiornato `Modules/Fixcity/app/View/Components/Blocks/TicketList/Agid.php` (line 68).
3. Aggiornato `Modules/Fixcity/app/View/Components/Blocks/TicketList.php`.
4. Pulite tutte le cache (`view`, `cache`, `bootstrap`, `optimize`).
5. Verificato che il sito `/it` restituisca 200 OK.

## Stato icone / search
- `it-search-wrapper` presente in HTML, sprite SVG `it-search` caricato.
- `it-socials` presente (d-none d-lg-flex / il secondo blocco visibile).
- Nessun errore server; eventuale mancanza visiva è CSS/JS client-side.

## Documentazione aggiornata
- Questo file docs aggiunto.
- AGENTS.md e MEMORY.md riferiscono l'iniziativa.
