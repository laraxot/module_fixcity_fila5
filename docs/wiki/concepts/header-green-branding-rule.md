# header green branding rule

## Regola di Branding Locale

Nonostante i riferimenti standard di Design Comuni (Bootstrap Italia) utilizzino il Blu (#0066CC), il modulo Fixcity adotta per questa istanza una **Branding Agnostica basata sul Logo del Comune**.

### Definizione Colori

1. **Sfondo Logo Comune (Verde)**: Rappresenta il colore primario dell'amministrazione.
   - Token: `var(--dc-green)` -> `#007A52`.
2. **Sfondo Top Slim (Verde Scuro)**: Utilizzato per la barra istituzionale superiore.
   - Token: `var(--dc-green-dark)` -> `#00402B`.

### Applicazione Obbligatoria (Parity Parity)

Tutti i componenti dell'header devono seguire questa scala cromatica per garantire la parità con l'identità del Comune:

- **Slim Header Wrapper**: Verde Scuro (#00402B).
- **Center Header Wrapper**: Verde (#007A52).
- **Navbar Header Wrapper**: Verde (#007A52).
- **CTA "Accedi all'area personale"**: Deve utilizzare lo sfondo Verde (#007A52) per non creare "Blue Spots" incongruenti.
- **Link Secondari (Iscrizioni, Estate, etc.)**: Sfondo Verde (#007A52).

## Rationale

La parità visuale non è solo copiare i token di un framework, ma rispettare la **gerarchia cromatica dell'ente locale** all'interno della struttura architettonica del framework.
