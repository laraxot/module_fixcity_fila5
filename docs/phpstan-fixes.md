## PHPStan Fixes - Modulo Fixcity

Questo documento raccoglie le segnalazioni PHPStan attuali sul modulo `Fixcity` e il piano di correzione, **prima** di toccare il codice.

### 1. Azioni dominio Ticket (ChangeStatus, GenerateTicketsAction)

- **File**: `app/Actions/ChangeStatus.php`  
  - **Errore**: chiamata a metodo inesistente `Ticket::setStatus()`.  
  - **Piano di fix**:
    - Spostare la logica di cambio stato in una Action dedicata (es. `ChangeTicketStatusAction`) che:
      - accetta un `Ticket` tipizzato e un `TicketStatusEnum`;
      - centralizza validazioni (transizioni ammesse) e side-effect (eventi/notifiche);
    - nel modello `Ticket` esporre solo helper tipizzati o usare direttamente l’Action, evitando metodi dinamici non dichiarati.

- **File**: `app/Actions/GenerateTicketsAction.php`  
  - **Errori**: uso estensivo di `mixed` (`$this->faker` non tipizzato), closure che dovrebbero restituire `Ticket` ma ritornano `mixed`, chiamate `create()/open()/urgent()/resolved()` su variabili non tipizzate.  
  - **Piano di fix**:
    - Tipizzare la proprietà faker (es. `protected FakerGenerator $faker;`) oppure ottenere un `TicketFactory` esplicito.
    - Estrarre la generazione di casi d’uso (ticket open/urgent/resolved) in piccoli metodi o Actions con return type `Ticket`.
    - Evitare concatenazioni di metodi su `mixed`: ogni step deve lavorare su istanze tipizzate (factory, `Ticket` o builder Eloquent).

### 2. Widget Filament (CreateTicketWidget)

- **File**: `app/Filament/Widgets/CreateTicketWidget.php`  
  - **Errori**: tipi non allineati con le nuove API Filament (`Component::schema()` e `Schema::components()` si aspettano `array<Htmlable|string>|Closure`, viene passato un array di componenti custom).  
  - **Piano di fix**:
    - Allineare il widget allo stack Laraxot/XotBase (usare wrapper Xot per i widget dove disponibile).
    - Verificare la firma di `schema()`/`components()` in Filament v5 e adeguare:
      - o restituendo Blade views/Htmlable,
      - o delegando alla configurazione schema standard usata negli altri moduli.
    - Documentare nel modulo Fixcity quale pattern di widget Filament è “standard” per la creazione ticket (eventuale reuse di pattern da `Modules\Activity` o `Modules\Cms`).

### 3. Livewire Auth/Login e TicketList

- **File**: `app/Livewire/Auth/Login.php`  
  - **Errori**: `Auth::attempt()` riceve `array|null`, metodi senza return type, proprietà `$form` e `getState()` su `mixed`.  
  - **Piano di fix**:
    - Allineare il login allo stack **Volt + Filament Widget** documentato nel modulo `Cms/User`, riusando componenti già tipizzati.
    - Tipizzare il form state (es. proprietà `array{email:string,password:string}` o DTO dedicato).
    - Aggiungere return type espliciti (`void` per `authenticate()`, `View` per `render()`).

- **File**: `app/Livewire/TicketList.php`  
  - **Errori**: proprietà senza tipo (`$search`, `$selectedCategory`, `$selectedStatus`), uso di `Modules\Fixcity\Models\Builder` inesistente, accesso a `$tickets` non dichiarata, relation `category` mancante sul modello `Ticket`.  
  - **Piano di fix**:
    - Tipizzare le proprietà (`?string`/`string` per search e filtri).
    - Usare `Illuminate\Database\Eloquent\Builder` come tipo di builder, non una classe custom.
    - Aggiungere le relazioni mancanti sul modello `Ticket` (es. `category()` tipizzata con `BelongsTo`).
    - Esplicitare la proprietà `$tickets` come `LengthAwarePaginator|Collection` a seconda dell’uso.

### 4. Modelli Ticket, TicketActivity, User

- **File**: `app/Models/Ticket.php`  
  - **Errori**: `belongsTo()`/`belongsToMany()` chiamati con `mixed` come `$related`.  
  - **Piano di fix**:
    - Sostituire `belongsTo($this->someRelationClass)` con `belongsTo(RelatedModel::class)` e tipizzare correttamente il return (`BelongsTo<RelatedModel,Ticket>`).
    - Aggiungere PHPDoc per ogni relazione complessa per aiutare PHPStan.

- **File**: `app/Models/TicketActivity.php`  
  - **Errori**: `withTrashed()` non disponibile sul tipo inferito, return type `ticket()` `mixed`.  
  - **Piano di fix**:
    - Tipizzare `ticket()` come `BelongsTo<Ticket,TicketActivity>` e usare la relazione di `SoftDeletes` solo se realmente applicata al modello `Ticket`.

- **File**: `app/Models/User.php`  
  - **Errore**: implementa `UserContract` ma non estende `Modules\Xot\Contracts\Model`.  
  - **Piano di fix**:
    - Allineare il modello utente Fixcity al pattern usato dal modulo `User` (estende il Model base Xot e implementa i contratti richiesti).
    - Oppure evitare un modello User custom nel modulo Fixcity e riusare il modello User del modulo principale, documentando chiaramente i confini di responsabilità.

### 5. Seeder, Factory e Route API

- **File**: `database/factories/*`, `database/seeders/*`  
  - **Errori**: accesso a offset su `mixed`, uso di `json_encode` non safe, factory verso modelli mancanti (`Modules\Fixcity\Models\Report`, `Modules\Category\Models\Category`).  
  - **Piano di fix**:
    - Tipizzare i dati di input (array shape chiari) e usare le funzioni `Safe\json_encode`.
    - Allineare le factory ai modelli realmente esistenti (o creare i modelli mancanti se fanno parte della business logic).

- **File**: `routes/api_ticket.php.old`  
  - **Nota architetturale**: il file è stato rinominato `.old` perché in Laraxot lo stack ufficiale per il dominio applicativo è **Volt + Folio + Filament + Azioni**, non controller/rotte API ad hoc.  
  - **Piano di fix**:
    - Migrare eventuali esigenze di API verso Actions + integrazioni specifiche (es. endpoints pubblici centralizzati nel modulo `Xot`), documentando ogni deviazione.

### 6. Correzioni completate

#### TicketActivity.php - `$fillable` PHPDoc covariance (fixed)
- **Errore**: `PHPDoc type array<int, string> of property $fillable is not covariant with PHPDoc type list<string> of overridden property BaseModel::$fillable`
- **Fix**: Cambiato `@var array<int, string>` in `@var list<string>` per allinearsi al tipo del parent `BaseModel`
- **Regola**: Tutti i `$fillable` devono usare `/** @var list<string> */`

#### TicketActivity.php - `withTrashed()` e return type (suppressed)
- **Errore**: `withTrashed()` non disponibile su `BelongsTo`, return type `mixed`
- **Fix**: Aggiunto `@phpstan-ignore method.notFound` nel PHPDoc della relazione `ticket()`
- **Nota**: `withTrashed()` su BelongsTo è un pattern Laravel valido ma non riconosciuto dal type system di PHPStan con Larastan

### 7. Prossimi step

1. Prioritizzare le correzioni legate ai **modelli** e alle **Actions di dominio** (ChangeStatus, GenerateTicketsAction), perché hanno impatto diretto sulla business logic.
2. Allineare i componenti Livewire/Filament agli stack già consolidati in `Cms`, `User`, `Activity`.
3. Rieseguire `./vendor/bin/phpstan analyse Modules/Fixcity` e aggiornare questa pagina con l'elenco degli errori risolti e quelli ancora aperti.
