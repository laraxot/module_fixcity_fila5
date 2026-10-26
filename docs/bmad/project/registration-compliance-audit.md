---
title: "Audit registrazione, consensi e cancellazione account"
type: audit
status: open
updated: 2026-10-14
owner: "Gdpr + User + Fixcity + titolare/DPO"
qmd: "registrazione utente consenso termini privacy GDPR disiscrizione cancellazione account PA"
related:
  - ./stories/5.3.registration-consent-evidence.story.md
  - ./stories/5.4.marketing-revocation.story.md
  - ./stories/5.5.subject-rights-account-lifecycle.story.md
  - ./stories/5.6.verified-registration-multilingual.story.md
  - ./stories/6.14.legal-acceptance-version-evidence.story.md
  - ./stories/9.3.legal-privacy-owner-approval.story.md
tags: [docs, fixcity, bmad]
---

# Audit registrazione, consensi e cancellazione account

## Esito

Il progetto **non è ancora pronto per la consegna alla PA** sul percorso account.
La registrazione possiede un controllo tecnico sui due checkbox obbligatori e sul
marketing facoltativo, ma non dimostra ancora una catena completa e approvata di:

`informativa pubblicata → accettazione distinta → evidenza versionata → account verificato →
revoca/disiscrizione → accesso, export, anonimizzazione o cancellazione con conservazioni motivate`.

Questo audit non sostituisce il parere del titolare, DPO o consulente legale. I testi,
le basi giuridiche, i tempi di conservazione e il destino delle segnalazioni devono
essere forniti e approvati dall'ente.

## Evidenze dal codice

| Area | Stato osservato | Rischio |
|---|---|---|
| Registrazione | `Modules\\Gdpr\\Filament\\Widgets\\Auth\\RegisterWidget` richiede `privacy_accepted` e `terms_accepted`; marketing è `false` di default | Tecnica presente, semantica legale non ancora approvata |
| Link legali | La vista `Themes/Sixteen/resources/views/filament/widgets/auth/register.blade.php` mostra testo semplice per i consensi; le chiavi HTML esistono nelle traduzioni ma non sono usate | L'utente non può aprire informativa e termini direttamente prima dell'invio |
| Evidenza | `SaveGdprConsentsAction` salva tipo, timestamp, IP e user-agent, ma non locale, identificativo/versione/hash del documento | Prova del consenso non è riconducibile al testo accettato |
| Base giuridica | Il form parla di accettazione della privacy; per una PA la finalità del servizio può dipendere da compito di interesse pubblico/obbligo legale, non da consenso | Rischio di consenso obbligatorio non libero o non necessario |
| Verifica email | `AUTH_MUST_VERIFY_EMAIL` è una configurazione; `RegisterWidget` effettua login anche quando la verifica è attiva | Account potenzialmente operativo prima della verifica; requisito da decidere e rendere coerente |
| Localizzazione | `routes/web_tall.php` limita alcune rotte auth a `it`/`en`, mentre le suite e le traduzioni dichiarano anche `de`, `es`, `fr`, `ru` | Flusso multilingue incoerente e link legali non uniformi |
| Marketing | Il consenso viene registrato e il modello supporta `revoked_at`, ma non è stata trovata una schermata o azione pubblica di revoca/disiscrizione | Art. 21 non dimostrabile end-to-end |
| Cancellazione | Esistono `DeleteUserAction`, `DeleteAccountWidget` e una pagina profilo Volt; il widget non gestisce il risultato dell'action e l'eliminazione diretta non definisce il destino di ticket, consensi, audit e obblighi PA | Cancellazione incompleta, perdita di evidenza o dati di interesse pubblico |
| Diritti | La story DCP-140 documenta assenza di export e canale completo per richieste dell'interessato | Mancano accesso, portabilità, rettifica, opposizione, richiesta registrata e scadenza |
| Testi legali | `config/local/fixcity/lang/it/terms.md` è ancora un placeholder; `policy.md` è una bozza generica con richiesta di consenso | Blocco di pubblicazione e di accettazione informata |

## Regola di registrazione da implementare

Il flusso finale deve distinguere esplicitamente:

1. presa visione dell'informativa privacy pubblicata, con link e versione;
2. accettazione dei termini del servizio, separata e con link e versione;
3. consensi facoltativi, in particolare marketing, non preselezionati e revocabili;
4. preferenze cookie anonime, separate dai consensi autenticati Gdpr;
5. verifica dell'indirizzo email e, se richiesto dall'ente, blocco delle funzioni
   protette fino alla verifica;
6. registrazione dell'account con informativa su diritti e canali di contatto.

La privacy notice non deve essere usata come consenso generico se la finalità ha
un'altra base giuridica: questa è una decisione del titolare/DPO, non del codice.

## Regola di disiscrizione e diritti

L'utente deve poter:

- revocare il marketing senza cancellare l'account;
- vedere lo stato dei consensi e la data/versione registrata;
- chiedere accesso, rettifica, limitazione, opposizione, portabilità e cancellazione;
- esportare i propri dati in formato strutturato;
- ottenere un esito e una motivazione quando la cancellazione completa è limitata
  da obblighi legali o dal compito di interesse pubblico;
- ricevere comunicazione dei tempi di risposta e del canale DPO.

La cancellazione tecnica non può essere un semplice `$user->delete()`: deve applicare
la matrice approvata per profilo, consensi, ticket, allegati, notifiche, log e audit,
con anonimizzazione o conservazione motivata dove necessario.

## Gate di consegna PA

Il gate resta **rosso** finché non sono verdi le story 5.3–5.6, 6.14, DCP-140 e
9.3. La firma del titolare/DPO deve coprire ogni locale e tenant abilitato, non solo
la schermata italiana.
