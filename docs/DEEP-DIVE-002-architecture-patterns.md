---
title: "Deep Dive: Architecture Patterns & Technical Decisions"
type: analysis
tags: [fixcity, architecture, patterns, technical-decisions, scalability]
status: draft
created: 2026-06-17
---

# Deep Dive: Architecture Patterns & Technical Decisions

## 1. Competitor Architecture Comparison

### 1.1 FixMyStreet - Perl Monolith

```
┌─────────────────────────────────────────────────────────┐
│              FIXMYSTREET ARCHITECTURE                   │
├─────────────────────────────────────────────────────────┤
│  Frontend                                                │
│  ├─ Perl Catalyst Framework                              │
│  ├─ Template Toolkit (HTML)                              │
│  ├─ OpenLayers (maps)                                    │
│  └─ jQuery (legacy JS)                                   │
├─────────────────────────────────────────────────────────┤
│  Backend                                                 │
│  ├─ Perl modules (CPAN)                                  │
│  ├─ PostgreSQL                                           │
│  ├─ Memcached                                            │
│  └─ Email (SendGrid/Exim)                                │
├─────────────────────────────────────────────────────────┤
│  Integration                                             │
│  ├─ Open311 API (rarely used)                            │
│  ├─ Email parsing (primary)                              │
│  └─ MapIt API (boundary matching)                        │
└─────────────────────────────────────────────────────────┘
```

**Criticità:**
- **Skill gap:** Perl developers rari nel 2024
- **Monolite:** Difficile estendere, deploy risky
- **Email-centric:** Non event-driven, polling-based

---

### 1.2 SeeClickFix - Ruby SaaS

```
┌─────────────────────────────────────────────────────────┐
│              SEECLICKFIX ARCHITECTURE                   │
├─────────────────────────────────────────────────────────┤
│  Client Layer                                            │
│  ├─ React Native (iOS/Android)                           │
│  ├─ React.js (web)                                       │
│  └─ Ruby on Rails (admin)                                │
├─────────────────────────────────────────────────────────┤
│  API Layer                                               │
│  ├─ Ruby on Rails API                                    │
│  ├─ GraphQL (partial)                                    │
│  └─ Open311 compliant endpoints                          │
├─────────────────────────────────────────────────────────┤
│  Core Services                                           │
│  ├─ Request Management                                   │
│  ├─ Duplicate Detection (geohash)                        │
│  ├─ GIS Routing (shapefile matching)                     │
│  ├─ Notification Engine                                  │
│  └─ Analytics (basic)                                    │
├─────────────────────────────────────────────────────────┤
│  Data Layer                                              │
│  ├─ PostgreSQL                                           │
│  ├─ Redis (cache/sessions)                               │
│  ├─ Elasticsearch (search)                               │
│  └─ S3 (images)                                          │
└─────────────────────────────────────────────────────────┘
```

**Pattern interessanti:**
- **Geohash clustering:** `geohash.encode(lat, lng, precision=7)` per 150m radius
- **GIS Shapefile routing:** Upload shapefile → automatic department assignment
- **Multi-tenant SaaS:** Single instance, multi-council

---

### 1.3 Decoro Urbano - PHP Legacy

```
┌─────────────────────────────────────────────────────────┐
│             DECORO URBANO ARCHITECTURE                │
├─────────────────────────────────────────────────────────┤
│  Client                                                  │
│  ├─ iOS Native (Objective-C, iOS 12+)                  │
│  ├─ Android Native (Java)                                │
│  └─ Web PHP/HTML                                         │
├─────────────────────────────────────────────────────────┤
│  Backend                                                 │
│  ├─ PHP Monolith                                         │
│  ├─ MySQL                                                │
│  ├─ Google Maps API                                      │
│  └─ FCM (push notif)                                     │
├─────────────────────────────────────────────────────────┤
│  Admin                                                   │
│  └─ Custom PHP CMS                                        │
└─────────────────────────────────────────────────────────┘
```

**Criticità:**
- Single database, no read replicas
- Vertical scaling only
- Last major update ~2018

---

### 1.4 Degradozero - Serverless

```
┌─────────────────────────────────────────────────────────┐
│             DEGRADZERO ARCHITECTURE                   │
├─────────────────────────────────────────────────────────┤
│  Frontend (GitHub Pages)                                 │
│  ├─ Static HTML/JS                                        │
│  ├─ Leaflet.js                                            │
│  ├─ EXIF.js (GPS extraction)                            │
│  └─ Nominatim (reverse geocoding)                       │
├─────────────────────────────────────────────────────────┤
│  "Backend" (Google)                                      │
│  ├─ Google Apps Script (JS)                              │
│  ├─ Google Sheets (DB!)                                  │
│  ├─ Google Drive (images)                                │
│  └─ GitHub Actions (CSV export)                         │
├─────────────────────────────────────────────────────────┤
│  Data Flow                                               │
│  1. Form → Apps Script                                   │
│  2. Apps Script → Sheets                                 │
│  3. Drive → images                                       │
│  4. Actions → CSV                                        │
│  5. GitHub Pages ← CSV                                  │
└─────────────────────────────────────────────────────────┘
```

**Innovazione:** Zero server management
**Limite:** Scale ceiling (5M cells Sheets limit)

---

## 2. Recommended Architecture for Fixcity

### 2.1 Target Architecture

```
┌─────────────────────────────────────────────────────────┐
│               FIXCITY TARGET ARCHITECTURE               │
├─────────────────────────────────────────────────────────┤
│                                                          │
│  ┌─────────────────────────────────────────────────────┐│
│  │              PRESENTATION LAYER                     ││
│  │  ┌──────────┐ ┌──────────┐ ┌──────────┐          ││
│  │  │ Web App  │ │ Mobile   │ │ Admin    │          ││
│  │  │(Filament)│ │ (Flutter)│ │ (Filament)│          ││
│  │  └──────────┘ └──────────┘ └──────────┘          ││
│  └─────────────────────────────────────────────────────┘│
│                       │                                  │
│  ┌─────────────────────────────────────────────────────┐│
│  │              API GATEWAY (Laravel)                  ││
│  │         Auth, Rate Limit, Routing                   ││
│  └─────────────────────────────────────────────────────┘│
│                       │                                  │
│  ┌─────────────────────────────────────────────────────┐│
│  │              CORE SERVICES                          ││
│  │  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌────────┐││
│  │  │ Ticket   │ │ Question-│ │ Timeline │ │ Notify │││
│  │  │ Service  │ │ naire    │ │ Service  │ │ Service│││
│  │  └──────────┘ └──────────┘ └──────────┘ └────────┘││
│  │  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌────────┐││
│  │  │ GIS      │ │ Duplicate│ │ Analytics│ │ Workflow│││
│  │  │ Service  │ │ Detect   │ │ Engine   │ │ Engine  │││
│  │  └──────────┘ └──────────┘ └──────────┘ └────────┘││
│  └─────────────────────────────────────────────────────┘│
│                       │                                  │
│  ┌─────────────────────────────────────────────────────┐│
│  │              DATA LAYER                             ││
│  │  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌────────┐││
│  │  │PostgreSQL│ │  Redis   │ │MeiliSearch│ │ MinIO │││
│  │  │(primary) │ │ (cache)  │ │ (search) │ │(images)││
│  │  └──────────┘ └──────────┘ └──────────┘ └────────┘││
│  └─────────────────────────────────────────────────────┘│
│                                                          │
└─────────────────────────────────────────────────────────┘
```

---

### 2.2 Technology Stack Decisions

| Component | Choice | Alternative Rejected | Rationale |
|-----------|--------|---------------------|-----------|
| **Backend** | Laravel 11 | Symfony, Ruby on Rails | Filament integration, ecosystem, team expertise |
| **Frontend Web** | Filament v3 | Custom React, Vue | Admin + public forms same framework |
| **Mobile** | Flutter | React Native, Native | Single codebase, native performance |
| **Database** | PostgreSQL 15 | MySQL 8 | PostGIS, JSONB, mature |
| **Cache** | Redis | Memcached | Sessions, queues, real-time |
| **Search** | MeiliSearch | Elasticsearch, Algolia | Easy self-host, typo-tolerant |
| **Storage** | MinIO | AWS S3 direct | Self-host option, S3-compatible |
| **Queue** | Laravel Horizon | RabbitMQ, Kafka | Redis-based, monitoring UI |
| **Maps** | Leaflet + OSM | Google Maps, Mapbox | Free, no API key, privacy |
| **Notifications** | Laravel Notify | Custom | Multi-channel abstraction |

---

### 2.3 Scalability Phases

#### Phase 1: Launch (0-10k users)
```
Single Server:
├─ Laravel + Nginx
├─ PostgreSQL (same server)
├─ Redis (same server)
└─ Cost: ~€20/month (Hetzner/Vultr)
```

#### Phase 2: Growth (10k-100k users)
```
Split Architecture:
├─ App Server: Laravel + Nginx
├─ DB Server: PostgreSQL
├─ Cache Server: Redis
├─ CDN: CloudFlare (images)
└─ Cost: ~€100/month
```

#### Phase 3: Scale (100k+ users)
```
Kubernetes Cluster:
├─ 3x App Pods (Laravel)
├─ 2x DB Pods (PostgreSQL + read replicas)
├─ 2x Cache Pods (Redis Cluster)
├─ Object Storage: MinIO cluster
├─ Load Balancer: Nginx/Traefik
└─ Cost: ~€500-1000/month
```

---

## 3. Key Technical Patterns

### 3.1 Duplicate Detection Algorithm

**SeeClickFix approach (basic):**
```python
# Geohash + time window
def is_duplicate(lat, lng, category, time_window=24h):
    geohash = encode(lat, lng, precision=7)  # ~150m radius
    similar = Ticket.where('geohash', geohash)
                        .where('category', category)
                        .where('created_at', '>', now() - time_window)
                        .count()
    return similar > 0
```

**Fixcity AI-powered approach:**
```python
# Multi-factor detection
def detect_duplicates(new_ticket):
    candidates = []
    
    # 1. Spatial clustering (geohash 150m)
    nearby = Ticket.near(new_ticket.location, radius=150m)
    
    for candidate in nearby:
        score = 0
        
        # 2. Image similarity (Computer Vision)
        if new_ticket.image and candidate.image:
            img_sim = image_hash_similarity(new_ticket.image, candidate.image)
            score += img_sim * 0.4  # 40% weight
        
        # 3. Text similarity (NLP)
        if new_ticket.description and candidate.description:
            text_sim = cosine_similarity(
                embed(new_ticket.description),
                embed(candidate.description)
            )
            score += text_sim * 0.3  # 30% weight
        
        # 4. Time proximity
        time_diff = abs(new_ticket.created_at - candidate.created_at)
        if time_diff < 24h:
            score += 0.2  # 20% weight
        
        # 5. Category match
        if new_ticket.category == candidate.category:
            score += 0.1  # 10% weight
        
        if score > 0.85:
            candidates.append((candidate, score))
    
    return sorted(candidates, key=lambda x: x[1], reverse=True)
```

**Implementation Laravel:**
```php
// app/Services/DuplicateDetectionService.php
class DuplicateDetectionService
{
    public function findDuplicates(Ticket $ticket, float $threshold = 0.85): Collection
    {
        // Spatial query with PostGIS
        $nearby = Ticket::query()
            ->selectRaw('*, ST_Distance(location, ?) as distance', [$ticket->location])
            ->whereRaw('ST_DWithin(location::geography, ?::geography, 150)', [$ticket->location])
            ->where('id', '!=', $ticket->id)
            ->where('created_at', '>', now()->subDays(7))
            ->get();
            
        $duplicates = collect();
        
        foreach ($nearby as $candidate) {
            $score = $this->calculateSimilarity($ticket, $candidate);
            if ($score >= $threshold) {
                $duplicates->push([
                    'ticket' => $candidate,
                    'score' => $score,
                    'factors' => $this->getSimilarityFactors(),
                ]);
            }
        }
        
        return $duplicates->sortByDesc('score');
    }
    
    private function calculateSimilarity(Ticket $a, Ticket $b): float
    {
        $weights = [
            'image' => 0.40,
            'text' => 0.30,
            'time' => 0.20,
            'category' => 0.10,
        ];
        
        $scores = [
            'image' => $this->imageSimilarity($a->image, $b->image),
            'text' => $this->textSimilarity($a->description, $b->description),
            'time' => $this->timeSimilarity($a->created_at, $b->created_at),
            'category' => ($a->category === $b->category) ? 1.0 : 0.0,
        ];
        
        return collect($scores)->map(fn($s, $k) => $s * $weights[$k])->sum();
    }
    
    private function imageSimilarity(?Image $a, ?Image $b): float
    {
        if (!$a || !$b) return 0.0;
        
        // Perceptual Hash (pHash)
        $hashA = $a->perceptual_hash;
        $hashB = $b->perceptual_hash;
        
        // Hamming distance
        $distance = $this->hammingDistance($hashA, $hashB);
        return max(0, 1 - ($distance / 64)); // Normalize to 0-1
    }
    
    private function textSimilarity(?string $a, ?string $b): float
    {
        if (!$a || !$b) return 0.0;
        
        // Use OpenAI embeddings or local sentence-transformers
        $embeddingA = $this->getEmbedding($a);
        $embeddingB = $this->getEmbedding($b);
        
        return $this->cosineSimilarity($embeddingA, $embeddingB);
    }
}
```

---

### 3.2 Timeline & State Machine Pattern

**State transitions:**
```php
// app/StateMachines/TicketStateMachine.php
class TicketStateMachine
{
    protected array $transitions = [
        'submitted' => ['acknowledged', 'rejected'],
        'acknowledged' => ['assigned', 'rejected'],
        'assigned' => ['in_progress'],
        'in_progress' => ['completed', 'on_hold'],
        'completed' => ['verified', 'reopened'],
        'verified' => ['closed'],
        'on_hold' => ['in_progress'],
        'reopened' => ['acknowledged'],
        'rejected' => ['reopened'], // appeal
    ];
    
    protected array $timestamps = [
        'submitted' => 'reported_at',
        'acknowledged' => 'acknowledged_at',
        'assigned' => 'assigned_at',
        'in_progress' => 'started_at',
        'completed' => 'completed_at',
        'verified' => 'verified_at',
        'closed' => 'closed_at',
    ];
    
    public function transition(Ticket $ticket, string $toState, ?User $actor = null): void
    {
        $fromState = $ticket->status->value;
        
        if (!in_array($toState, $this->transitions[$fromState] ?? [])) {
            throw new InvalidStateTransitionException(
                "Cannot transition from {$fromState} to {$toState}"
            );
        }
        
        DB::transaction(function () use ($ticket, $fromState, $toState, $actor) {
            // Update ticket
            $ticket->status = $toState;
            $ticket->{$this->timestamps[$toState]} = now();
            $ticket->{"{$toState}_by"} = $actor?->id;
            $ticket->save();
            
            // Create history record
            TicketStatusHistory::create([
                'ticket_id' => $ticket->id,
                'from_status' => $fromState,
                'to_status' => $toState,
                'changed_by' => $actor?->id,
                'changed_at' => now(),
            ]);
            
            // Dispatch event
            event(new TicketStatusChanged($ticket, $fromState, $toState, $actor));
        });
    }
}
```

---

### 3.3 Questionnaire Builder Pattern

**Database Schema:**
```php
// Dynamic form schema
Schema::create('questionnaires', function (Blueprint $table) {
    $table->id();
    $table->string('ticket_type'); // FK to enum
    $table->json('schema'); // Form definition
    $table->json('scoring_rules'); // Priority calculation
    $table->timestamps();
});

// Example schema JSON:
{
  "fields": [
    {
      "key": "buca_dimensione",
      "type": "select",
      "label": "Dimensione buca",
      "options": [
        {"value": "small", "label": "< 30cm", "priority": 10},
        {"value": "medium", "label": "30-50cm", "priority": 25},
        {"value": "large", "label": "> 50cm", "priority": 50}
      ],
      "required": true
    },
    {
      "key": "pericolo_immediato",
      "type": "boolean",
      "label": "Pericolo immediato?",
      "conditional_logic": {
        "priority_boost": 100
      }
    }
  ],
  "scoring_formula": "sum(field_priorities) + conditional_boosts"
}
```

**Rendering (Filament):**
```php
// app/Forms/Components/DynamicQuestionnaire.php
class DynamicQuestionnaire extends Component
{
    public function build(Schema $schema): array
    {
        $components = [];
        
        foreach ($schema->fields as $field) {
            $component = match($field['type']) {
                'select' => Select::make($field['key'])
                    ->options(collect($field['options'])->pluck('label', 'value'))
                    ->required($field['required'] ?? false),
                    
                'boolean' => Toggle::make($field['key'])
                    ->label($field['label'])
                    ->required($field['required'] ?? false),
                    
                'number' => TextInput::make($field['key'])
                    ->numeric()
                    ->minValue($field['validation']['min'] ?? null)
                    ->maxValue($field['validation']['max'] ?? null),
                    
                'rating' => Rating::make($field['key'])
                    ->stars(5),
                    
                default => TextInput::make($field['key']),
            };
            
            // Apply conditional logic
            if (isset($field['conditional_logic'])) {
                $component->visible(fn ($get) => $this->evaluateCondition(
                    $field['conditional_logic'], 
                    $get
                ));
            }
            
            $components[] = $component;
        }
        
        return $components;
    }
}
```

---

## 4. Performance Optimizations

### 4.1 Database Indexing Strategy

```php
// Migration for performance
Schema::table('tickets', function (Blueprint $table) {
    // Spatial index for location queries
    $table->spatialIndex('location');
    
    // Composite for filtering
    $table->index(['status', 'type', 'created_at']);
    $table->index(['assigned_to', 'status']);
    
    // For timeline queries
    $table->index(['reported_at', 'acknowledged_at', 'completed_at']);
    
    // Full-text search (PostgreSQL)
    $table->fullText(['title', 'description']);
});
```

### 4.2 Caching Strategy

```php
// config/cache.php strategy
'ticket' => [
    'view' => 3600,        // 1 hour
    'list' => 300,         // 5 minutes
    'stats' => 600,        // 10 minutes
    'heatmap' => 1800,     // 30 minutes
]

// Usage in controller
public function show(Ticket $ticket)
{
    return Cache::remember("ticket:{$ticket->id}:full", 3600, function () use ($ticket) {
        return $ticket->load([
            'reporter', 'assignedTo', 'statusHistory', 'answers', 'media'
        ]);
    });
}
```

### 4.3 Image Optimization Pipeline

```php
// app/Jobs/ProcessTicketImage.php
class ProcessTicketImage implements ShouldQueue
{
    public function handle(): void
    {
        $image = $this->ticketImage;
        
        // 1. Resize to max 1920x1080
        $resized = Image::make($image->path)
            ->resize(1920, 1080, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        
        // 2. Compress (quality 80)
        $resized->encode('jpg', 80);
        
        // 3. Generate thumbnail (400x300)
        $thumbnail = Image::make($image->path)
            ->fit(400, 300);
        
        // 4. Upload to MinIO/S3
        Storage::disk('minio')->put(
            "tickets/{$image->ticket_id}/{$image->id}.jpg", 
            $resized->stream()
        );
        Storage::disk('minio')->put(
            "tickets/{$image->ticket_id}/{$image->id}_thumb.jpg", 
            $thumbnail->stream()
        );
        
        // 5. Generate perceptual hash for duplicate detection
        $image->update([
            'perceptual_hash' => $this->calculatePHash($resized),
            'processed_at' => now(),
        ]);
    }
}
```

---

## 5. Security Patterns

### 5.1 Data Protection (GDPR)

```php
// app/Models/Ticket.php - Privacy by design
class Ticket extends Model
{
    // Automatic PII encryption
    protected $encrypted = ['reporter_email', 'reporter_phone'];
    
    // Anonymization after retention period
    public function anonymize(): void
    {
        $this->update([
            'reporter_name' => 'Anonymous',
            'reporter_email' => null,
            'reporter_phone' => null,
            'anonymized_at' => now(),
        ]);
        
        // Delete associated PII
        $this->comments()->where('is_internal', false)->delete();
    }
    
    // Scope for retention policy
    public function scopeReadyForAnonymization($query)
    {
        return $query->where('created_at', '<', now()->subYears(5))
                    ->whereNull('anonymized_at');
    }
}

// Scheduled job
Schedule::command('tickets:anonymize-old')->monthly();
```

### 5.2 Rate Limiting

```php
// app/Providers/RouteServiceProvider.php
RateLimiter::for('ticket-creation', function (Request $request) {
    // 5 tickets per hour per IP
    return Limit::perHour(5)->by($request->ip());
});

RateLimiter::for('api', function (Request $request) {
    // 100 requests per minute per user
    return Limit::perMinute(100)->by($request->user()?->id ?: $request->ip());
});
```

---

## 6. Monitoring & Observability

### 6.1 Key Metrics

```php
// app/Metrics/TicketMetrics.php
class TicketMetrics
{
    public function register(): void
    {
        // Business metrics
        Metrics::counter('tickets_created_total', 'Total tickets created');
        Metrics::counter('tickets_resolved_total', 'Total tickets resolved');
        Metrics::histogram('ticket_resolution_time', 'Time to resolution in hours', [1, 4, 24, 72, 168]);
        Metrics::gauge('tickets_open_current', 'Currently open tickets');
        
        // Technical metrics
        Metrics::histogram('api_response_time', 'API response time in ms', [50, 100, 250, 500, 1000]);
        Metrics::counter('duplicate_detection_runs', 'Duplicate detection algorithm runs');
    }
}
```

### 6.2 Health Checks

```php
// app/HealthChecks/DatabaseConnectionCheck.php
class DatabaseConnectionCheck extends Check
{
    public function run(): Result
    {
        try {
            DB::select('SELECT 1');
            return Result::make()->ok();
        } catch (Exception $e) {
            return Result::make()->failed($e->getMessage());
        }
    }
}

// app/HealthChecks/StorageConnectionCheck.php
class StorageConnectionCheck extends Check
{
    public function run(): Result
    {
        try {
            Storage::disk('minio')->exists('health-check.txt');
            return Result::make()->ok();
        } catch (Exception $e) {
            return Result::make()->failed($e->getMessage());
        }
    }
}
```

---

## 7. Deployment Strategy

### 7.1 CI/CD Pipeline

```yaml
# .github/workflows/deploy.yml
name: Deploy

on:
  push:
    branches: [main]

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
      
      - name: Install dependencies
        run: composer install --no-dev
      
      - name: Run PHPStan
        run: vendor/bin/phpstan analyse --memory-limit=2G
      
      - name: Run tests
        run: vendor/bin/pest
      
      - name: Build assets
        run: npm ci && npm run build

  deploy:
    needs: test
    runs-on: ubuntu-latest
    steps:
      - name: Deploy to production
        uses: deployphp/action@v1
        with:
          private-key: ${{ secrets.SSH_PRIVATE_KEY }}
          deployer-binary: 'vendor/bin/dep'
          
# deploy.php
namespace Deployer;

require 'recipe/laravel.php';

set('application', 'fixcity');
set('repository', 'git@github.com:laraxot/fixcity.git');

host('production')
    ->setHostname('fixcity.it')
    ->setRemoteUser('deployer')
    ->setDeployPath('/var/www/fixcity');

task('deploy:build', function () {
    cd('{{release_path}}');
    run('npm ci && npm run build');
});

task('deploy:migrate', function () {
    cd('{{release_path}}');
    run('php artisan migrate --force');
});

after('deploy:update_code', 'deploy:build');
after('deploy:symlink', 'deploy:migrate');
```

---

## 8. Conclusion

### Key Architectural Decisions

1. **Microservices vs Monolith:** Monolith Laravel (faster dev, easier deploy) con service extraction per ML/AI tasks
2. **Self-hosted vs Cloud:** Hybrid - core self-hosted, ML models via API
3. **Mobile Strategy:** Flutter (cost-effective, native performance)
4. **Database:** PostgreSQL + PostGIS (best GIS support)
5. **Real-time:** Laravel Reverb (WebSockets) + polling fallback

### Scalability Guarantees

- **0-10k users:** Single server, no optimization needed
- **10k-100k users:** Horizontal scaling with load balancer
- **100k+ users:** Kubernetes with auto-scaling

---

*Architecture analysis based on: FixMyStreet GitHub, SeeClickFix API docs, Decoro Urbano reverse engineering, Degradozero open source code, Laravel best practices.*
