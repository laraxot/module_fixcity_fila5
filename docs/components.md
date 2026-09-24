# Componenti View di Fixcity

## TicketList

### Agid Component
Il componente `Agid` è un componente view Laravel che gestisce la visualizzazione della lista dei ticket secondo le linee guida AGID.

#### Struttura
- Estende: `Illuminate\View\Component`
- Namespace: `Modules\Fixcity\View\Components\Blocks\TicketList`
- View Template: `fixcity::components.blocks.ticket_list.agid`

#### Metodi Principali

##### `getReports(): Collection`
Recupera la lista dei report dal database con le seguenti informazioni:
- id
- titolo
- descrizione
- localizzazione (formato JSON)
- indirizzo
- categoria
- stato
- metadata (formato JSON)
- data di creazione

I dati vengono ordinati per data di creazione in ordine decrescente.

##### `getCategories(): Collection`
Recupera la lista delle categorie dal database con:
- id
- nome
- descrizione
- icona

#### Note Tecniche
- Il componente utilizza query dirette al database tramite `DB::table()`
- Gli stati dei report sono gestiti tramite l'enum `ReportStatusEnum`
- I campi JSON (location e metadata) vengono decodificati automaticamente

#### Correzioni Recenti
- Risolto un problema di riferimento circolare nella definizione della classe
- Rimossa l'estensione errata `BaseAgid` in favore di `Component` 