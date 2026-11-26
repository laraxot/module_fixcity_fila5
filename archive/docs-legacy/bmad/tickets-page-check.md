---
type: bmad-initiative
status: open
updated: 2026-10-17
initiative_id: kp-tickets-page-check
summary: |
  Controllo pagina /it/tickets (e /it/events, /it/domande-frequenti): database MariaDB corretto (mariadb, non sqlite), componente ticket_list migliorato con try/catch in mount() e empty state nel render. Verifica locale: DB connesso (Ticket::count() = 0 nello sviluppo, ma query funziona senza errori). Non raggiungibile il server remoto (192.168.1.40) dalla rete attuale, ma la correzione DB e la qualità del codice sono applicate.
references:
  - laravel/Modules/Fixcity/resources/views/components/blocks/ticket_list/ticket_list.blade.php
  - laravel/config/database.php
  - laravel/.env
