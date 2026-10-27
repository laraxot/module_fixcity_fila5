---
title: "Brainstorming BMAD — demo blog ed eventi realistici"
type: brainstorming-report
status: decision-input
updated: 2026-10-15
tags: [bmad, second-brain, blog, eventi, demo, commenti, media, ponytail]
qmd: "obiettivo fixcity"
---

# Obiettivo

Preparare una demo FixCity credibile: contenuti editoriali in italiano, eventi
pubblici, immagini con testo alternativo e crediti, commenti approvati e almeno
una risposta. Nessun Lorem Ipsum, nessun testimonial presentato come reale e
nessun dato personale operativo.

## 5 Whys

1. Perché popolare il Blog? Per mostrare il valore del servizio, non una pagina
   vuota.
2. Perché includere eventi? Per dimostrare il passaggio da informazione a
   partecipazione civica.
3. Perché commenti e foto? Per rendere visibili fiducia, dialogo e contesto.
4. Perché non generare testo casuale? La demo deve spiegare il prodotto e non
   sembrare un fixture tecnico.
5. Perché non introdurre subito `Event`? Il caso attuale è editoriale; un nuovo
   dominio duplicherà authoring, media, SEO, tenant e route.

## Opzioni discusse

| Opzione | Valore | Costo/rischio | Esito |
|---|---|---|---|
| `Article` + `content_type=post` | Post agnostico, riusa Blog, SEO, media e route | Campi evento ancora minimi | GO |
| `Article` + `content_type=event` | Una sola Resource e tassonomia | Servono validazione date e query tipo | GO pilota |
| Modulo `Event` separato | Adatto a iscrizioni, capienza, ricorrenze, ICS | Nuovo dominio, migrazioni, tenant, policy e UI | NO per ora |
| URL immagini casuali | Veloce | Licenza e riproducibilità non verificabili | NO |
| Asset locali già presenti | Stabili e offline | Va registrata la licenza interna/demo | GO |
| Commenti casuali | Poco credibili e potenzialmente orfani | Relazioni non deterministiche | NO |
| Catalogo narrativo deterministico | Realistico, idempotente, localizzabile | Più righe curate | GO |

## Mind map del contenuto

```text
FixCity demo
├── Blog
│   ├── onboarding cittadino
│   ├── trasparenza del ciclo ticket
│   ├── accessibilità e inclusione
│   └── casi prima/dopo
├── Eventi
│   ├── passeggiata accessibilità
│   ├── laboratorio digitale
│   ├── cura condivisa del parco
│   └── incontro pubblico
├── Media
│   ├── immagine locale
│   ├── alt text
│   ├── caption
│   └── credito/licenza
└── Dialogo
    ├── commenti approvati
    ├── una risposta dell'organizzatore
    └── un commento pending per mostrare moderazione
```

## Decisione tecnica: `post` agnostico

- Nel linguaggio prodotto usare **post**: è agnostico rispetto a blog, novità,
  guida o evento.
- Nel codice mantenere `Modules\Blog\Models\Article` (rename → Post = deferred);
  usare `content_type=post` per il post editoriale e `content_type=event` per
  l’evento. Il campo `type` è riservato a Parental STI — mai tassonomia.
  Dettaglio: [brainstorming-post-vs-article.md](../../../Blog/docs/brainstorming-post-vs-article.md).
- Usare `Modules\Comment\Models\Comment` tramite `HasComments` di Article;
  non usare la vecchia relazione Blog `ticket_comments`.
- Aggiungere un catalogo/seeder demo dedicato, con guardia `local|testing|demo`.
- Riutilizzare Spatie Media Library solo se il resolver pubblico mostra davvero
  la collection; in caso contrario usare `main_image_url` con asset locali.
- Conservare immagini, alt e crediti nel contratto già disponibile (`content_blocks`
  e/o `custom_properties`), senza creare un modulo Media parallelo.
- Non cambiare il dominio in `Event` finché non servono iscrizioni, capienza,
  ricorrenze, più sessioni, calendario ICS, pagamento o notifiche operative.

## Dataset pilota

- 8 post editoriali `content_type=post`, con almeno 4 visibili in homepage/lista;
- 4 eventi `content_type=event` futuri e 1 evento concluso per verificare le liste;
- 4 categorie civiche, non categorie generiche Animals/Mountains/People/Things;
- 2 profili demo già presenti, mai email personali reali;
- 2 commenti approvati per articolo/evento, una risposta su contenuti selezionati;
- 1 commento pending per verificare la moderazione;
- lingua primaria `it`, con fallback esplicito e nessun testo Lorem Ipsum;
- seed deterministico e rieseguibile senza duplicati.

## Rischi e guardrail

- I dati demo devono essere marcati e confinati agli ambienti ammessi.
- Non usare immagini remote non attribuite in una demo offline o pubblica.
- Non simulare iscrizioni evento con un semplice URL non implementato.
- Non esporre commenti pending nel front office.
- Verificare tenant isolation prima di usare dati reali.
- Aggiornare le query con `where('content_type', 'event')` (non Parental `type`).

## Decisione per il team

**Implementare ora il catalogo demo con `Article.content_type=post|event`,
Comment e media riusati.** Preparare una story separata per i gap di
authoring/query/SEO. Aprire
un modulo `Event` soltanto dopo un requisito operativo verificabile.

## Output agenti

La discussione parallela ha confermato: il modulo Blog possiede già `type`, date
evento, blocchi e Media Library; Comment possiede morph, approvazione e risposte;
Media ha già `custom_properties` per alt/licenza. Il seeder Fixcity è il pattern
di riferimento per guardie ambiente, cataloghi curati e idempotenza.

## Sintesi agenti (2026-10-15)

- **Architect:** evento = `content_type=event` su Article; Comment morph; no modulo Event.
- **Content:** titoli pillar civici, eventi con luogo/orario, anti-Lorem, email `*@fixcity.demo`.
- **QA:** assert 8 post + 5 event; commenti via modulo Comment; credit nei blocks; env guard.

Implementazione: `Modules\Blog\Database\Seeders\DemoContentSeeder` +
`ArticleFactory::demoPost/demoEvent` + `FixcityDemoSeeder` step 7/7.
Story: IM-BLOG-007 (done).
