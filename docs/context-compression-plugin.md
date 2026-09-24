# Context compression (approccio interno)

Scopo: ridurre la lunghezza del testo prima di inviare richieste a LLM per evitare errori di contesto (token limit).

Sintesi:
- "Contextual compression" è una tecnica che genera una rappresentazione più corta del contesto mantenendo i fatti essenziali.
- Se è possibile, usare un modello di compressione dedicato (es. modello di summarization) prima di chiamare l'API principale.
- Se non è possibile usare il modello, applicare un fallback extractive (estrarre frasi chiave fino a raggiungere la dimensione target).

Implementazione nel progetto:
- Aggiunto `Modules\\Xot\\Services\\ContextCompressor` che prova a usare l'OpenAI PHP client se disponibile (e fallisce in modo sicuro), altrimenti usa un metodo estrattivo.

Uso:
- Chiamare `\Modules\\Xot\\Services\\ContextCompressor::compress($longText, $targetChars)` prima di costruire la chiamata API.

Passi successivi:
- (Opzionale) Installare `openai-php/client` via Composer e configurare `OPENAI_API_KEY` per usare compressione via modello.
- Testare con casi reali di input per verificare qualità compressa.

Riferimenti:
- Ricerca web: "contextual compression"; alcune pagine ufficiali OpenAI possono essere soggette a restrizioni di accesso.