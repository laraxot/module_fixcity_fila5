---
title: "STORY-404: Dashboard & Analytics Admin"
type: story
tags: [fixcity, dashboard, analytics, charts, reports, statistics, decoro-urbano, degradozero]
status: draft
priority: medium
assignee: TBD
created: 2026-06-17
updated: 2026-06-17
---

# STORY-404: Dashboard & Analytics Admin

## User Story

**Come** amministratore comunale / manager operativo  
**Voglio** una dashboard con statistiche e report sulle segnalazioni  
**Per** prendere decisioni basate su dati, allocare risorse, monitorare SLA e trend

## Background & Motivazione

**Competitor Analysis:**
- **Decoro Urbano**: Statistiche scaricabili, report per comune, filtri per categoria
- **Degradozero**: Dashboard grafici (per categoria, urgenza, stato, andamento temporale), dark mode, export CSV
- **SeeClickFix**: API per integrazione con Power BI / Socrata, data viz tools
- **FixMyStreet**: Survey post-risoluzione per metriche qualitative

**KPIs Critici per PA:**
1. Volume segnalazioni per periodo
2. Tempi medi di risoluzione (MTTR)
3. SLA compliance rate
4. Distribuzione geografica (heatmaps)
5. Trend categorie (emerging issues)
6. Carico lavoro per operatore/tecnico

## Acceptance Criteria

### AC1: Dashboard Overview
```gherkin
Dato un amministratore loggato
Quando accede a "/admin/dashboard"
Allora vede:
  - Metriche cards: Totali, Aperte, In corso, Risolte oggi
  - Grafico andamento segnalazioni (line chart: 30/60/90 giorni)
  - Distribuzione per stato (doughnut chart)
  - Distribuzione per categoria (bar chart)
  - Mappa heatmap segnalazioni aperte
  - Top 5 zone con più segnalazioni
  - Alert SLA in scadenza (< 24h)
```

### AC2: Filtri e Date Range
```gherkin
Dato un report qualsiasi
Quando applico filtri
Allora posso filtrare per:
  - Date range (picker con preset: oggi, 7gg, 30gg, custom)
  - Categoria ticket (multi-select)
  - Stato (multi-select)
  - Zona/Quartiere (multi-select)
  - Operatore assegnato
  - Urgenza/Priorità
  - Tag
E tutti i grafici si aggiornano in tempo reale
```

### AC3: Report Dettagliati
```gherkin
Dato un amministratore
Quando clicca "Report"
Allora può generare:
  - Report operativi: ticket per stato, per operatore, tempo medio risoluzione
  - Report geografici: heatmap perdensità, zone critiche
  - Report temporali: trend orari/giornalieri/settimanali
  - Report qualità: soddisfazione cittadino (survey results), reopen rate
E esportare in: PDF, Excel/CSV, JSON API
```

### AC4: SLA Monitoring
```gherkin
Dato un sistema con SLA configurati
Quando visualizzo dashboard SLA
Allora vedo:
  - SLA Acknowledge: % ticket acknowledged entro 24h
  - SLA Resolution: % ticket resolved entro target per priorità
  - Breach list: ticket che hanno violato SLA con durata breach
  - Trend SLA: andamento compliance nel tempo
  - Alert configurabili (email se SLA < 90%)
```

### AC5: Geospatial Analytics
```gherkin
Dato ticket con coordinate GPS
Quando visualizzo mappa analitica
Allora vedo:
  - Heat map cluster segnalazioni (rosso = alta densità)
  - Filtro per categoria sulla mappa (toggle layer)
  - Drill-down: click cluster → lista ticket
  - Analisi perimetro: disegna zona → conta ticket interni
  - Comparazione zone: confronta metriche tra quartieri
```

### AC6: Operatore Performance
```gherkin
Dato un manager
Quando visualizza "Team Performance"
Allora vedo:
  - Ticket assegnati per operatore
  - Ticket completati per operatore (oggi/settimana/mese)
  - Tempo medio risoluzione per operatore
  - Rating/soddisfazione per operatore (da survey)
  - Workload attuale (ticket in carico per operatore)
  - Ranking operatore
```

### AC7: Predictive Insights
```gherkin
Dato dati storici sufficienti (> 6 mesi)
Quando visualizzo "Insights"
Allora sistema mostra:
  - Previsione volume prossima settimana (ML simple trend)
  - Categorie in crescita (emerging issues)
  - Zone a rischio (alta densità + bassa risoluzione)
  - Raccomandazioni: "Aumentare personale settore X"
```

### AC8: Widgets Customizzabili
```gherkin
Dato un amministratore
Quando configura dashboard
Allora può:
  - Aggiungere/rimuovere widgets
  - Riordinare drag-and-drop
  - Ridimensionare widgets
  - Salvare layout personalizzato
  - Condividere dashboard con altri utenti
```

## Technical Notes

### Database Schema (Materialized Views)

```php
// Migration: ticket_statistics_hourly (per performance)
Schema::create('ticket_statistics_hourly', function (Blueprint $table) {
    $table->id();
    $table->datetime('hour');
    $table->string('ticket_type', 50)->nullable();
    $table->string('status', 50)->nullable();
    $table->string('zone', 100)->nullable();
    $table->integer('count')->default(0);
    $table->integer('avg_resolution_minutes')->nullable();
    $table->integer('sla_breach_count')->default(0);
    $table->timestamps();
    
    $table->unique(['hour', 'ticket_type', 'status', 'zone']);
    $table->index(['hour', 'ticket_type']);
});

// Migration: operator_performance_daily
Schema::create('operator_performance_daily', function (Blueprint $table) {
    $table->id();
    $table->date('date');
    $table->foreignId('user_id')->constrained();
    $table->integer('tickets_assigned')->default(0);
    $table->integer('tickets_completed')->default(0);
    $table->integer('avg_resolution_minutes')->nullable();
    $table->decimal('satisfaction_score', 3, 2)->nullable(); // 1.00 - 5.00
    $table->timestamps();
    
    $table->unique(['date', 'user_id']);
});
```

### Aggregazione Dati (Scheduled Job)

```php
// app/Console/Commands/AggregateTicketStatistics.php
class AggregateTicketStatistics extends Command
{
    protected $signature = 'tickets:aggregate-stats {--date=} {--rebuild}';
    
    public function handle(): void
    {
        $date = $this->option('date') ? Carbon::parse($this->option('date')) : now();
        $rebuild = $this->option('rebuild');
        
        if ($rebuild) {
            TicketStatisticsHourly::where('hour', '>=', $date->copy()->subDays(30))->delete();
        }
        
        // Aggrega per ultima ora
        $hour = $date->copy()->startOfHour();
        
        $stats = Ticket::query()
            ->whereBetween('created_at', [$hour, $hour->copy()->endOfHour()])
            ->selectRaw('
                DATE_FORMAT(created_at, "%Y-%m-%d %H:00:00") as hour,
                type,
                status,
                zone,
                COUNT(*) as count,
                AVG(TIMESTAMPDIFF(MINUTE, created_at, completed_at)) as avg_resolution
            ')
            ->groupBy('hour', 'type', 'status', 'zone')
            ->get();
            
        foreach ($stats as $stat) {
            TicketStatisticsHourly::updateOrCreate(
                [
                    'hour' => $stat->hour,
                    'ticket_type' => $stat->type,
                    'status' => $stat->status,
                    'zone' => $stat->zone,
                ],
                [
                    'count' => $stat->count,
                    'avg_resolution_minutes' => $stat->avg_resolution,
                ]
            );
        }
        
        // Aggrega SLA breaches
        $breaches = Ticket::query()
            ->whereNotNull('acknowledged_at')
            ->whereRaw('TIMESTAMPDIFF(HOUR, created_at, acknowledged_at) > 24')
            ->whereBetween('acknowledged_at', [$hour, $hour->copy()->endOfHour()])
            ->count();
            
        TicketStatisticsHourly::where('hour', $hour)
            ->update(['sla_breach_count' => $breaches]);
    }
}

// routes/console.php
Schedule::command('tickets:aggregate-stats')->hourly();
Schedule::command('tickets:aggregate-stats --rebuild')->dailyAt('02:00');
```

### Filament Dashboard Widgets

```php
// app/Filament/Widgets/TicketStatsOverview.php
class TicketStatsOverview extends BaseWidget
{
    protected function getCards(): array
    {
        $filters = $this->getFilters();
        $dateRange = $this->getDateRange();
        
        return [
            Stat::make('Totali', $this->getTotalCount($dateRange))
                ->description('Segnalazioni totali')
                ->descriptionIcon('heroicon-m-document-text')
                ->chart($this->getTrendData(7))
                ->color('primary'),
                
            Stat::make('Aperte', $this->getOpenCount())
                ->description('Da gestire')
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color('danger'),
                
            Stat::make('Risolte Oggi', $this->getResolvedTodayCount())
                ->description('Completate nelle ultime 24h')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
                
            Stat::make('MTTR', $this->getMeanTimeToResolution($dateRange) . 'h')
                ->description('Tempo medio risoluzione')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
        ];
    }
    
    private function getTrendData(int $days): array
    {
        return Ticket::query()
            ->where('created_at', '>=', now()->subDays($days))
            ->groupByRaw('DATE(created_at)')
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->pluck('count')
            ->toArray();
    }
}

// app/Filament/Widgets/TicketsByStatusChart.php
class TicketsByStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Stato Segnalazioni';
    protected static ?string $maxHeight = '300px';
    protected int|string|array $columnSpan = 1;
    
    protected function getData(): array
    {
        $data = Ticket::query()
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();
            
        return [
            'labels' => $data->pluck('status')->map(fn ($s) => TicketStatusEnum::from($s)->label()),
            'datasets' => [
                [
                    'data' => $data->pluck('count'),
                    'backgroundColor' => [
                        '#EF4444', // submitted - red
                        '#F59E0B', // acknowledged - amber
                        '#3B82F6', // assigned - blue
                        '#8B5CF6', // in_progress - purple
                        '#10B981', // completed - green
                        '#059669', // verified - emerald
                        '#6B7280', // closed - gray
                    ],
                ],
            ],
        ];
    }
    
    protected function getType(): string
    {
        return 'doughnut';
    }
}

// app/Filament/Widgets/TicketsTrendChart.php
class TicketsTrendChart extends ChartWidget
{
    protected static ?string $heading = 'Andamento 30 Giorni';
    protected int|string|array $columnSpan = 2;
    
    protected function getData(): array
    {
        $days = 30;
        $data = TicketStatisticsHourly::query()
            ->where('hour', '>=', now()->subDays($days)->startOfDay())
            ->groupByRaw('DATE(hour)')
            ->selectRaw('DATE(hour) as date, SUM(count) as total')
            ->orderBy('date')
            ->get();
            
        return [
            'labels' => $data->pluck('date')->map(fn ($d) => Carbon::parse($d)->format('d/m')),
            'datasets' => [
                [
                    'label' => 'Segnalazioni',
                    'data' => $data->pluck('total'),
                    'borderColor' => '#3B82F6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
        ];
    }
    
    protected function getType(): string
    {
        return 'line';
    }
}
```

### Map Component (Leaflet + Heatmap)

```php
// app/Filament/Pages/TicketHeatmapPage.php
class TicketHeatmapPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-map';
    protected static string $view = 'filament.pages.ticket-heatmap';
    
    public ?TicketTypeEnum $filterType = null;
    public array $filterStatus = [];
    public string $dateRange = '30'; // days
    
    protected function getViewData(): array
    {
        $query = Ticket::query()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('created_at', '>=', now()->subDays((int) $this->dateRange));
            
        if ($this->filterType) {
            $query->where('type', $this->filterType);
        }
        
        if (!empty($this->filterStatus)) {
            $query->whereIn('status', $this->filterStatus);
        }
        
        $points = $query->select('id', 'latitude', 'longitude', 'type', 'status', 'priority_score')
            ->get()
            ->map(fn ($t) => [
                'lat' => $t->latitude,
                'lng' => $t->longitude,
                'intensity' => $t->priority_score / 100, // 0-1 for heatmap
                'type' => $t->type->value,
                'status' => $t->status->value,
                'url' => route('filament.admin.resources.tickets.view', $t),
            ]);
            
        return [
            'points' => $points,
            'zones' => $this->getZoneBoundaries(),
        ];
    }
    
    private function getZoneBoundaries(): array
    {
        // Ritorna geojson dei quartieri/zone
        return Zone::all()->map(fn ($z) => [
            'name' => $z->name,
            'geojson' => $z->boundary_geojson,
            'ticket_count' => $z->tickets()->open()->count(),
        ])->toArray();
    }
}
```

### Export Reports

```php
// app/Actions/Reports/GenerateTicketReportAction.php
class GenerateTicketReportAction
{
    use QueueableAction;
    
    public function execute(
        ReportTypeEnum $type,
        DateRangeData $dateRange,
        ?array $filters = null,
        ExportFormatEnum $format = ExportFormatEnum::PDF
    ): string {
        $data = match($type) {
            ReportTypeEnum::OPERATIONAL => $this->getOperationalData($dateRange, $filters),
            ReportTypeEnum::GEOGRAPHIC => $this->getGeographicData($dateRange, $filters),
            ReportTypeEnum::TEMPORAL => $this->getTemporalData($dateRange, $filters),
            ReportTypeEnum::QUALITY => $this->getQualityData($dateRange, $filters),
        };
        
        return match($format) {
            ExportFormatEnum::PDF => $this->exportToPdf($type, $data, $dateRange),
            ExportFormatEnum::EXCEL => $this->exportToExcel($type, $data),
            ExportFormatEnum::CSV => $this->exportToCsv($type, $data),
            ExportFormatEnum::JSON => $this->exportToJson($type, $data),
        };
    }
    
    private function getOperationalData(DateRangeData $range, ?array $filters): array
    {
        return [
            'summary' => [
                'total_tickets' => Ticket::inDateRange($range)->count(),
                'by_status' => Ticket::inDateRange($range)->groupBy('status')->count(),
                'by_type' => Ticket::inDateRange($range)->groupBy('type')->count(),
            ],
            'performance' => [
                'avg_resolution_time' => Ticket::resolved()->inDateRange($range)->avgResolutionTime(),
                'sla_compliance' => Ticket::inDateRange($range)->slaComplianceRate(),
                'reopen_rate' => Ticket::inDateRange($range)->reopenRate(),
            ],
            'by_operator' => User::operators()->map(fn ($op) => [
                'name' => $op->name,
                'assigned' => $op->assignedTickets()->inDateRange($range)->count(),
                'completed' => $op->completedTickets()->inDateRange($range)->count(),
                'avg_time' => $op->completedTickets()->inDateRange($range)->avgResolutionTime(),
            ]),
        ];
    }
    
    private function exportToPdf(ReportTypeEnum $type, array $data, DateRangeData $range): string
    {
        $pdf = PDF::loadView('reports.tickets', [
            'type' => $type,
            'data' => $data,
            'range' => $range,
            'generated_at' => now(),
        ]);
        
        $filename = "report-{$type->value}-{$range->start->format('Y-m-d')}.pdf";
        $path = "reports/{$filename}";
        Storage::put($path, $pdf->output());
        
        return $path;
    }
}
```

### Simple Predictions (ML Optional)

```php
// app/Services/PredictionService.php
class PredictionService
{
    /**
     * Previsione semplice basata su media mobile
     */
    public function predictNextWeekVolume(): array
    {
        // Ultimi 8 settimane
        $weeklyData = Ticket::query()
            ->where('created_at', '>=', now()->subWeeks(8))
            ->groupByRaw('YEARWEEK(created_at)')
            ->selectRaw('YEARWEEK(created_at) as week, COUNT(*) as count')
            ->pluck('count')
            ->toArray();
            
        if (count($weeklyData) < 4) {
            return ['prediction' => null, 'confidence' => 0, 'method' => 'insufficient_data'];
        }
        
        // Media mobile semplice (last 4 weeks)
        $last4Weeks = array_slice($weeklyData, -4);
        $prediction = (int) round(array_sum($last4Weeks) / 4);
        
        // Confidence basata su varianza
        $variance = $this->calculateVariance($last4Weeks);
        $confidence = max(0, min(100, 100 - ($variance * 10)));
        
        return [
            'prediction' => $prediction,
            'confidence' => round($confidence, 1),
            'method' => 'simple_moving_average',
            'last_weeks_avg' => round(array_sum($last4Weeks) / 4, 1),
            'trend' => $this->calculateTrend($weeklyData),
        ];
    }
    
    public function getEmergingCategories(): array
    {
        // Categorie in crescita (> 50% incremento vs mese precedente)
        $currentMonth = Ticket::thisMonth()->groupBy('type')->count();
        $lastMonth = Ticket::lastMonth()->groupBy('type')->count();
        
        $emerging = [];
        foreach ($currentMonth as $type => $count) {
            $previous = $lastMonth[$type] ?? 0;
            if ($previous > 0 && (($count - $previous) / $previous) > 0.5) {
                $emerging[] = [
                    'type' => $type,
                    'previous_count' => $previous,
                    'current_count' => $count,
                    'growth_percent' => round((($count - $previous) / $previous) * 100, 1),
                ];
            }
        }
        
        return $emerging;
    }
    
    private function calculateTrend(array $data): string
    {
        $firstHalf = array_sum(array_slice($data, 0, count($data) / 2)) / (count($data) / 2);
        $secondHalf = array_sum(array_slice($data, count($data) / 2)) / (count($data) / 2);
        
        if ($secondHalf > $firstHalf * 1.1) return 'increasing';
        if ($secondHalf < $firstHalf * 0.9) return 'decreasing';
        return 'stable';
    }
}
```

## Definition of Done

- [ ] Database aggregations (hourly/daily stats tables)
- [ ] Scheduled job per popolamento statistiche
- [ ] Filament widgets: StatsOverview, Charts (doughnut, line, bar)
- [ ] Map component con heatmap Leaflet
- [ ] SLA monitoring dashboard
- [ ] Operator performance page
- [ ] Report generation (PDF, Excel, CSV, JSON)
- [ ] Predictive insights (simple trend analysis)
- [ ] Export scheduling (email report giornaliero/settimanale)
- [ ] API endpoint per dati raw (`/api/analytics/*`)
- [ ] Test unitari per calcoli aggregati
- [ ] Test feature per report generation
- [ ] Documentazione per interpretazione KPIs
- [ ] PHPStan level max passa

## Related Issues

- Epic: EPIC-006 Analytics & Reporting
- Depends on: STORY-401 (Timeline - dati temporali), STORY-402 (Questionnaire - dati categorizzati)
- Blocks: STORY-405 (API Open311 - dati esposti)

## Discussion Links

- GitHub Issue: `https://github.com/laraxot/fixcity/issues/404`
- GitHub Discussion: `https://github.com/laraxot/fixcity/discussions/404`

## Competitor References

- Degradozero Dashboard: https://github.com/gbvitrano/Segnalazioni
- SeeClickFix Open Data: https://www.civicplus.help/seeclickfix/docs/available-seeclickfix-api
