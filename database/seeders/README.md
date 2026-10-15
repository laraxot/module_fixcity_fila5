# FixCity Seeders Documentation

**Purpose**: Comprehensive demo data generation for investor presentations and Feature Complete demonstrations.

## Overview

This seeders directory provides realistic demo data for the FixCity civic reporting platform. The seeders generate:
- 27 presentation tickets (GeoJSON + database)
- Multiple cities across Italy for multi-regional demonstration
- User accounts (citizen, operator, admin)
- Comment history and interactions
- Category and ticket type data
- Complete GeoJSON exports for map integration

## Investor Demo Value

### What Demonstrates
- Real-world civic engagement flows
- Multi-city, multi-language capabilities
- Complete citizen-to-authority communication
- Real-time map integration
- Advanced filtering and search

### Key Metrics Showcased
- 27 geographic locations across 7 Italian cities
- Multi-language support (Italian + others)
- 3 user roles (citizen, operator, admin)
- Rich media attachments
- Comment threads and interactions
- Real-time status tracking

## Required Dependencies

1. Database migrations completed
2. User accounts seeded (DemoUsersSeeder)
3. Ticket categories seeded (CategorySeeder)
4. Demo tickets seeded (TicketDatabaseSeeder)
5. Demo cities seeded (DemoCitiesSeeder)
6. Ticket comments seeded (TicketCommentSeeder)
7. GeoJSON update and validation (GeoJsonUpdateSeeder)

## Seeders Order (Run in this sequence)

1. **DemoUsersSeeder.php** - Creates citizen/operator/admin accounts
2. **ProfileSeeder.php** - Creates user profiles
3. **CategorySeeder.php** - Creates ticket categories and types
4. **TicketDatabaseSeeder.php** - Creates base demo tickets
5. **DemoCitiesSeeder.php** - Multi-region expansion (7 cities)
6. **TicketCommentSeeder.php** - Comments and interactions
7. **TicketActivitySeeder.php** - Activity logs
8. **TicketHourSeeder.php** - Time tracking
9. **TicketRelationSeeder.php** - Complex relationships
10. **TicketSubscriberSeeder.php** - Subscription system
11. **GeoJsonUpdateSeeder.php** - Updates GeoJSON exports

## Seeders Generated

### DemoUsersSeeder.php
- 3 demo accounts (admin, citizen, operator)
- Passwords: 'password'
- Italian localization (lang: 'it')
- Required for all other seeders

### ProfileSeeder.php
- User profile data generation
- Profile completion demonstration
- Essential for user-facing features

### CategorySeeder.php
- Ticket type categories (8 categories)
- Active/inactive status management
- Parent/child category relationships
- Icon and description data

### TicketDatabaseSeeder.php
- **Total Tickets**: 20 base tickets + 7 multi-city tickets = 27
- **Geographic Coverage**: Mogliano Veneto (Treviso)
- **Ticket Types**: 8 types (road maintenance, lighting, waste, etc.)
- **Status Distribution**:
  - Open: 10 tickets
  - In Progress: 5 tickets
  - On Hold: 2 tickets
  - Resolved: 2 tickets
  - In Review: 1 ticket

#### Demo Ticket Samples:
1. **Road Maintenance**: Via Morandi (HIGH priority)
2. **Public Lighting**: Piazza Marconi (MEDIUM priority)
3. **Waste Collection**: Via Roma (MEDIUM priority)
4. **Urban Furniture**: Parco Belvedere (LOW priority)
5. **Parks & Gardens**: Via Piovan (HIGH priority)
6. **Sewage & Drainage**: Via Bachelet (MEDIUM priority)
7. **Public Buildings**: Via Don Bosco (HIGH priority)
8. **Public Transport**: Via Venezia (MEDIUM priority)
9. **Public Safety**: Via Terraglio (HIGH priority)
10. **Environmental**: Via Maroncelli (MEDIUM priority)

### DemoCitiesSeeder.php
- **Multi-Region Coverage**: 7 Italian cities
- **Geographic Diversity**:
  - Veneto (Mogliano Veneto)
  - Lombardy (Milan)
  - Tuscany (Florence)
  - Lazio (Rome)
  - Campania (Naples)
  - Piedmont (Turin)
  - Emilia-Romagna (Bologna)

### TicketCommentSeeder.php
- **Interactive Comments**: 6-10 comments per ticket
- **Conversation Flow**:
  - Citizen to Authority
  - Authority responses
  - Public comments
  - Internal notes
- **Status-Based Comments**:
  - Open: Receipt, assignment
  - In Progress: Updates, progress
  - Resolved: Completion confirmation

### Activity Logs, Hours, Relations, Subscribers
- Comprehensive tracking system
- Time logging and billing
- Complex relationships
- Notification subscriptions

## GeoJSON Structure (for Map Integration)

### File Location
```
public_html/data/tickets.json
```

### Schema
```json
{
  "type": "FeatureCollection",
  "generated_at": "2026-09-27T11:17:01.382169Z",
  "total": 27,
  "features": [
    {
      "type": "Feature",
      "geometry": {
        "type": "Point",
        "coordinates": [12.249756, 45.562246]
      },
      "properties": {
        "id": 1,
        "title": "Buca profonda in via Morandi",
        "type": {
          "value": "road_maintenance",
          "label": "Manutenzione Stradale",
          "iconUrl": "/assets/fixcity/svg/road-maintenance.svg"
        },
        "address": "Via Rodolfo Morandi 5, Mogliano Veneto",
        "city": "Mogliano Veneto",
        "status": {
          "value": "in_progress",
          "label": "In lavorazione",
          "color": "#ea580c"
        },
        "detail_url": "/it/tickets/1"
      }
    }
  ]
}
```

### Key Features
- **Coordinates**: WGS84 (latitude, longitude)
- **Properties**: All ticket metadata
- **Status Colors**: Visual status indicators
- **Detail URLs**: Direct links to ticket pages
- **Type Icons**: Categorized visual representation

## Integration Points for Investors

### 1. Backend API
- `/api/tickets` - CRUD operations
- `/api/tickets/{id}/comments` - Comment system
- `/api/tickets/geojson` - GeoJSON export

### 2. Frontend Demonstrations
- **Map View**: Interactive leaflet/mapbox integration
- **List View**: Ticket catalog with filtering
- **Detail View**: Single ticket with full information
- **Create View**: Ticket submission form

### 3. Admin Dashboard
- **Overview**: Key metrics and statistics
- **Ticket Management**: CRUD operations
- **User Management**: Account management
- **Analytics**: Usage and performance data

## Demo Scenarios

### Scenario 1: Citizen Experience
1. Citizen reports a problem
2. Ticket created with location and metadata
3. Citizen receives confirmation
4. Ticket tracked in real-time
5. Comments and updates received

### Scenario 2: Authority Experience
1. Authority receives ticket notification
2. Ticket assigned to team member
3. Work initiated and tracked
4. Progress updates shared
5. Resolution confirmed

### Scenario 3: Investor Demo
1. Launch with 20+ demo tickets
2. Show map integration
3. Demonstrate citizen-authority interaction
4. Show multi-city coverage
5. Display analytics and metrics

## Quality Gates

### Data Validation
- All coordinates validated
- Required fields present
- Status consistency
- Geographic coverage verified

### Technical Validation
- PHPStan 0 errors
- All tests passing
- No runtime errors
- Performance benchmarks met

### Presentation Validation
- Visual demo ready
- Investor materials prepared
- Documentation complete
- Demo scripts available

## Commands for Demo Setup

```bash
# Full demo setup (run in project root)
cd /mnt/nas07/var/www/_bases/base_fixcity_fila5/laravel
php artisan db:seed --class=DemoUsersSeeder
php artisan db:seed --class=CategorySeeder
php artisan db:seed --class=TicketDatabaseSeeder
php artisan db:seed --class=DemoCitiesSeeder
php artisan db:seed --class=TicketCommentSeeder
php artisan db:seed --class=GeoJsonUpdateSeeder

# Verify setup
php artisan tinker --execute="echo Ticket::count(); echo Comment::count(); echo Storage::disk('public')->exists('data/tickets.json') ? 'GeoJSON exists' : 'Missing GeoJSON';"
```

## Demo Readiness Checklist

### Pre-Demo
- [ ] All seeders executed successfully
- [ ] Database migrations completed
- [ ] GeoJSON files generated and accessible
- [ ] Frontend maps integrated
- [ ] API endpoints tested
- [ ] Performance benchmarks met

### During Demo
- [ ] Live data showcase
- [ ] Real-time interactions
- [ ] Map navigation
- [ ] Ticket creation/editing
- [ ] Comment threads
- [ ] Status tracking

### Post-Demo
- [ ] Demo scripts captured
- [ ] Investor materials prepared
- [ ] Documentation updated
- [ ] Metrics collected

## Success Metrics

### Technical Metrics
- **Database Records**: 50+ tickets
- **Comment Threads**: 100+ comments
- **Cities Covered**: 7 Italian cities
- **Languages Supported**: Italian + others
- **API Endpoints**: 10+ functional endpoints

### Business Metrics
- **Citizen Engagement**: 3 user roles
- **Resolution Time**: Tracked and optimized
- **Cost Savings**: Civic efficiency improvements
- **User Satisfaction**: Feedback collection

## Support Files

### Demo Scripts
- `scripts/demo-setup.sh` - Complete demo setup
- `scripts/demo-teardown.sh` - Cleanup
- `scripts/demo-presentation.sh` - Demo presentation

### Documentation
- This README.md
- `docs/seeders/usage.md` - Usage guide
- `docs/seeders/troubleshooting.md` - Common issues

### Configuration
- `.env.example` - Environment variables
- `config/demo.php` - Demo-specific configuration

## Next Steps

### Immediate (Week 1)
1. Execute all seeders
2. Verify GeoJSON generation
3. Test frontend map integration
4. Prepare demo scripts

### Short-term (Week 2-3)
1. Create demo documentation
2. Prepare investor presentation
3. Test all scenarios
4. Collect feedback

### Long-term (Month 1)
1. Performance optimization
2. Additional city/region additions
3. Advanced demo features
4. Production deployment planning

---

**Last Updated**: 2026-09-27
**Version**: 1.0
**Audience**: Investors, Developers, Demo Teams

---

*This documentation ensures consistent demo execution and presentation quality for all stakeholder audiences.*
