---
type: bmad-initiative
status: completed
updated: 2026-10-17
initiative_id: kp-faq-route-fix-verified
summary: |
  https://fixcity.eu/it/domande-frequenti verificato: HTTP 200 (62388 byte). Nessun RouteNotFoundException. Route 'pages.view' rimossa da faq.blade.php; sostituito con /accessibility. Cache view pulita. Documentazione aggiornata (BMAD + Second Brain + Filament Blueprint).
verification:
  url: https://fixcity.eu/it/domande-frequenti
  http: 200
  content_length: 62388
  error_found: false
  fix: faq.blade.php line 52 (route -> direct link)
  db: mariadb (non sqlite)
