# wizard map runtime asset chain

## contesto

Su `http://127.0.0.1:8000/fixcity/admin/tickets/create` la mappa del wizard risulta instabile/non coerente con l'esperienza attesa.

L'analisi runtime e' stata eseguita con login operatore e osservazione diretta di rete + snapshot UI.

## evidenze

- Login e accesso alla route admin ticket completati con successo.
- Richieste asset mappa:
  - `GET /modules/geo/map-picker.js` -> `200`
  - `GET /modules/geo/map-picker.css` -> `200`
  - `GET /modules/geo/geo-map-widget.js` -> `404`
  - `HEAD /modules/geo/geo.js` -> `404`
- Lo script servito da `/modules/geo/map-picker.js` e' un loader con fallback su `/themes/Geo/js/map-picker-component.js`.
- Il fallback e' un componente legacy lightweight (Leaflet da CDN) non allineato al contratto moderno del picker wizard.

## causa radice

La business logic del wizard richiede una catena asset deterministica: componente mappa unico, eventi coerenti, stato Livewire coerente.

In runtime invece avviene:

1. Il loader tenta `geo.js` modulo (mancante).
2. Cade su fallback tema legacy.
3. Il fallback non e' la stessa implementazione canonica usata dal flusso moderno.

Questo crea una violazione DRY/KISS: due famiglie di runtime map nello stesso flusso (canonica + fallback legacy), con rischio regressioni visuali e comportamentali.

## decisione architetturale proposta

- Tenere **una sola catena runtime** per il wizard admin ticket:
  - o si pubblica il bundle modulo canonico (`/modules/geo/geo.js`) e si elimina il fallback legacy;
  - oppure si sostituisce il loader con un import diretto e verificabile del componente canonico.
- Evitare fallback a componenti legacy non equivalenti nel flusso wizard.
- Mantenere la ownership:
  - modulo Geo: logica mappa/componente/eventi;
  - tema: solo layer visuale, non sostituzione di componenti runtime.

## acceptance criteria

- Nessun `404` su asset mappa richiesti dalla pagina ticket create.
- Nessun fallback legacy attivo nel percorso nominale.
- Mappa visibile e interattiva al primo ingresso nello step dati.
- Eventi coordinate coerenti con il contratto del field usato nel wizard.

## test plan

1. Aprire `http://127.0.0.1:8000/fixcity/admin/tickets/create`.
2. Verificare tab network:
   - assenza di `404` su asset mappa.
   - presenza solo degli asset canonici previsti.
3. Aprire step dati del wizard:
   - mappa renderizzata correttamente.
   - marker/zoom/ricerca operativi.
4. Verificare aggiornamento coordinate nello stato form.

## collegamenti

- [Fixcity wiki index](../wiki/index.md)
- [Fixcity wiki log](../wiki/log.md)
- [Fixcity runtime asset integrity](../wiki/concepts/segnalazione-runtime-asset-integrity.md)
- [Geo wiki index](../../Geo/docs/wiki/index.md)
- [Sixteen wiki index](../../../Themes/Sixteen/docs/wiki/index.md)
- [Root wiki index](../../../../docs/wiki/index.md)
