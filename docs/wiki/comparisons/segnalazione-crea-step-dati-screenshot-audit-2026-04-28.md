---
title: Segnalazione Crea Step Dati Screenshot Audit 2026-04-28
type: comparison
updated: 2026-04-28
status: active
---

# Segnalazione Crea Step Dati Screenshot Audit 2026-04-28

## Scope

- URL osservata: `http://127.0.0.1:8001/it/tests/segnalazione-crea?step=form.dati-della-segnalazione%3A%3Adata%3A%3Awizard-step`
- Fonte: screenshot runtime fornito dall'utente il `2026-04-28`
- Evidenza file: `/home/zorin/.cursor/projects/var-www-bases-base-fixcity-fila5/assets/c__Users_Marco_AppData_Roaming_Cursor_User_workspaceStorage_438e91e0bb0c7d9358628aa4b245213e_images_1-54d11cac-0e83-4f97-96fa-eb4889da33c8.png`
- Step: `Dati della segnalazione` (`2/3`)

## Findings

| ID | Severita' | Errore osservato | Impatto | Owner primario |
|---|---|---|---|---|
| FX-SHOT-01 | alta | Il riquadro sinistro `Informazioni richieste` occupa molto spazio ma risulta di fatto vuoto. | Introduce rumore visivo e sottrae focus al contenuto del form. | Fixcity widget/schema |
| FX-SHOT-02 | media | La nota `* I campi contraddistinti...` e il titolo `Luogo` risultano troppo distanti, con un vuoto verticale eccessivo prima del primo controllo utile. | Il flusso dello step appare spezzato e poco guidato. | Fixcity markup + Sixteen spacing |
| FX-SHOT-03 | alta | Il campo `Cerca un luogo...` mostra la lente di ingrandimento sovrapposta all'inizio del testo/placeholder. | Il primo input dello step sembra rotto gia' a colpo d'occhio e riduce la leggibilita' del campo. | Boundary widget Geo/Fixcity + CSS tema |
| FX-SHOT-04 | critica | La mappa mostra testo grezzo sovrapposto al layer cartografico, con sintomo simile a tag/markup/SVG non chiuso o non interpretato correttamente. | Il componente di selezione luogo non e' affidabile e puo' impedire il task principale dello step. | Geo runtime component |
| FX-SHOT-05 | alta | La mappa appare schiarita/opacizzata, con resa simile a un widget disabilitato. | L'utente non capisce se il widget sia attivo, disabilitato o incompleto. | Geo runtime + Sixteen visual layer |
| FX-SHOT-06 | media | Il marker rosa e' visibile ma il resto dell'interfaccia mappa non trasmette uno stato operativo coerente. | Stato ambiguo: sembra esserci un punto selezionato ma senza affordance per correggerlo o confermarlo. | Geo/Fixcity integration |

## Note di Boundary

- `Fixcity` resta owner dello schema dello step, della presenza/assenza di contenuti nella colonna laterale e della progressione semantica del wizard.
- `Geo` resta owner del runtime funzionale del picker mappa.
- `Sixteen` resta owner della leggibilita' visuale, dello spacing e della parity UI del flusso.

## Recheck minimo richiesto

- il box laterale non deve piu' apparire come contenitore vuoto dominante;
- il campo search deve mostrare la lente separata dal testo, con padding corretto;
- la mappa deve renderizzare solo UI cartografica valida, senza testo grezzo o frammenti tipo tag non chiuso sopra i tile;
- la mappa non deve apparire schiarita come se fosse disabilitata;
- i controlli base della mappa devono risultare visibili e coerenti con uno stato interattivo.

## Piano fix owner-side (modulo)

1. rimuovere il contenitore laterale "vuoto dominante" quando non ci sono voci informative effettive;
2. normalizzare la gerarchia contenuti (`nota obbligatorieta'` -> `titolo Luogo` -> `search` -> `mappa`) riducendo gap verticali nel markup;
3. validare che il blocco mappa non emetta stringhe grezze lato widget prima dell'hydration del componente Geo;
4. rieseguire audit screenshot dopo fix con Playwright MCP sulla stessa URL e stesso step query.
