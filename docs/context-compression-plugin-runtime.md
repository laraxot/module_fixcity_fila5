# context compression plugin runtime

## diagnosi

L'errore API `maximum context length ... use the context-compression plugin` viene dal provider/endpoint quando input + tool output + output richiesto superano la finestra del modello.

Per Fixcity il rischio e alto perche il modulo ha molte docs, wiki, screenshot report e file di confronto HTML. Le sessioni agent non devono caricare interi alberi documentali se una query mirata basta.

## regola Fixcity

- Prima consultare `docs/wiki/index.md` e `laravel/Modules/Fixcity/docs/wiki/index.md`.
- Usare `rg`/QMD con query strette.
- Documentare sintesi riusabili in `docs/wiki/` o `laravel/Modules/Fixcity/docs/wiki/`.
- Non incollare output lunghi di error page/debug HTML in chat o nel contesto.
- Per errori runtime, preferire log estratti e pattern mirati (`storage/logs/laravel.log`, `php -l`, route/page smoke).

## plugin

OpenRouter documenta `plugins: [{ "id": "context-compression" }]` come opzione per comprimere prompt troppo grandi. Questa impostazione va applicata nel client API/provider, non nel codice del modulo Fixcity.

Localmente e presente `context-mode` v1.0.89 per ridurre output tool e aiutare l'MCP, ma non modifica automaticamente le richieste OpenRouter.

