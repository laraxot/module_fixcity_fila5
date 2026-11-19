---
id: dcp-205
title: "DCP-205: test rossi, strumenti di prova e debito di qualita"
type: story
epic: dc-parity-v2
status: ready-for-dev
priority: P1
owner: module:Fixcity
module: Fixcity
created: 2026-10-15
updated: 2026-10-15
qmd: "test rossi geo meetup tenant hasteams ticketformrender notifications legacy pest-scratch-db exit code pint phpstan parity gate qmd"
related: [dcp-103, dcp-179, dcp-184, dcp-200]
tags: [test, qualita, pest, phpstan]
---

# DCP-205: test rossi, strumenti di prova e debito di qualita

## Fase BMAD
Verify. Misura del 2026-10-14 sera (regressione su Fixcity Feature `Filament`/`Wizard`/`Livewire`/`Pages`/`Auth`, Unit `Filament`, User Unit `Models`, Gdpr Unit `Filament`, Geo): 675 passati, 529 rossi. PHPStan `Modules` a zero. Story precedente sul tema: DCP-103.

## Da fare
- [ ] **`TicketFormRenderTest` (2 test).** `Component has errors: "data.location" => ["Indica un punto valido sulla mappa..."]`: il test manda `latitude`/`longitude`, lo schema del widget richiede `data.location`. Decisione DEC-23 (proposta: lo schema e' la fonte di verita'); riscrivere il test sul comportamento reale, non rimetterci i vecchi campi.
- [ ] **Geo, 515 test rossi per un motivo solo.** `realpath not find dir[.../Themes/Meetup/resources/views/pages]`: il bootstrap di test dipende dal tema `Meetup`. DEC-34 (proposta: togliere la dipendenza). Dopo, rimisurare: i rossi veri emergeranno solo allora.
- [ ] **User, 6 test rossi preesistenti.** `TenantTest` (2: il test passa un array a `settings`, che il modello dichiara `string|null`) e `Traits/HasTeamsTest` (4: `Target class [Illuminate\Database\Eloquent\ModelTeam] does not exist`). Capire lo scopo del trait prima di toccare test o modello.
- [ ] **`ticket_hours.user_id` bigint contro UUID** (DEC-35, aggiunta da un'altra sessione): 20 dei 27 test rossi di Fixcity dipendono da questo.
- [ ] **Tabella `notifications` legacy in `fixcity_data`** (bigint, diversa da `fixcity_user`), e `extras`/`profiles` con tipi diversi. DEC-33: verificare che nessun codice usi la connessione dati, poi migrazione in avanti **dopo approvazione**.
- [ ] **`pest-scratch-db.sh` esce 0 anche con test rossi.** Far propagare il codice di uscita di Pest (oggi `grep` finale lo maschera); test dello script con un test finto rosso.
- [ ] **Isolamento del database di prova.** Lo strumento ha corretto l'ordine di clonazione (prima `fixcity_user`); aggiungere un controllo che confronta i tipi delle colonne in comune fra i due database e avvisa.
- [ ] **Pint:** passarlo solo su file, mai su cartelle di modulo (ha riformattato 27 file di `Modules/Mobile`). Aggiungere la regola allo script di controllo, non solo alla memoria.
- [ ] **PHPStan sui test.** Oggi `phpstan analyse Modules` include i test dei moduli che hanno la config; verificare quali cartelle di test sono fuori perimetro e dichiararlo.
- [ ] **Parity pixel a zero foto (DCP-33) e CLS con foto non 4:3** (DCP-182): mai misurati; farlo con `parity-gate.sh`.
- [ ] **Indice QMD:** rigenerare `QMD_INDEX=fixcity-fila5` a lavoro fermo e solo dopo la rinomina dei file `.md` (DCP-201); verificare con una `qmd search`, non con il messaggio di `update`.

## Criteri di accettazione
- [ ] La regressione sopra e' ripetibile con un comando e il suo esito e' scritto qui con i numeri veri (passati, rossi, saltati).
- [ ] Ogni test ancora rosso ha una riga in questa story con causa e proprietario; nessun test cancellato o saltato senza motivo scritto.

## Non verificato
Esito dei test dopo la rimozione della dipendenza da `Meetup`; i 4 rossi di `HasTeamsTest` dipendono da una classe di Eloquent che questa versione di Laravel potrebbe non avere: non verificato.
