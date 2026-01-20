# Modulo FixCity

## Descrizione
FixCity è un modulo Laravel progettato per la gestione delle segnalazioni e dei problemi urbani. Permette ai cittadini di segnalare problematiche nella loro città e alle amministrazioni di gestirle in modo efficiente.

## Caratteristiche Principali
- Gestione segnalazioni cittadine
- Sistema di ticketing avanzato
- Gestione profili utente
- Pannello amministrativo Filament
- Supporto multilingua

## Requisiti
- PHP 8.2+
- Laravel 11.x
- Filament 3.x
- Modulo Xot installato

## Installazione
```bash
composer require modules/fixcity
php artisan module:migrate Fixcity
php artisan module:seed Fixcity
```

## Struttura
```
Modules/Fixcity/
├── app/
│   ├── Models/         # Modelli del dominio
│   ├── Providers/      # Service providers
│   └── Filament/       # Risorse Filament
├── config/            # Configurazioni
├── database/         # Migrazioni e seeders
├── resources/        # Views e assets
└── routes/           # Definizioni delle route
```

## Modelli Principali
- `Profile`: Gestione profili utente
- `User`: Gestione utenti
- `Ticket`: Gestione segnalazioni

## Integrazione con Filament
Il modulo utilizza Filament per il pannello amministrativo, offrendo:
- Dashboard personalizzata
- Gestione segnalazioni
- Gestione utenti
- Report e statistiche

## API
Il modulo espone API RESTful per:
- Creazione segnalazioni
- Aggiornamento stato
- Recupero informazioni
- Gestione profili

## Configurazione
```php
// config/fixcity.php
return [
    'notifications' => [
        'email' => true,
        'push' => false,
    ],
    'moderation' => [
        'enabled' => true,
        'auto_approve' => false,
    ]
];
```

## Testing
```bash
php artisan test --filter=Fixcity
```

## Contribuire
1. Fork il repository
2. Crea un branch (`git checkout -b feature/nome-feature`)
3. Commit le modifiche (`git commit -am 'Aggiunta feature'`)
4. Push al branch (`git push origin feature/nome-feature`)
5. Crea una Pull Request

## Licenza
MIT License
