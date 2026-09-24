---
title: "Deep Dive: Security Architecture & GDPR Compliance"
type: technical-spec
tags: [fixcity, security, gdpr, encryption, privacy, compliance, audit]
status: draft
created: 2026-06-17
---

# Deep Dive: Security Architecture & GDPR Compliance

## 1. Threat Model

### 1.1 STRIDE Analysis

| Threat | Vector | Impact | Mitigation |
|--------|--------|--------|------------|
| **Spoofing** | Fake admin accounts | Data manipulation | MFA, RBAC, audit logs |
| **Tampering** | Modify ticket status | Integrity loss | Immutable history, signatures |
| **Repudiation** | Deny action taken | Accountability loss | Non-repudiable audit trail |
| **Information Disclosure** | Leak personal data | GDPR violation, fines | Encryption at rest/transit |
| **Denial of Service** | Spam tickets | Service unavailability | Rate limiting, CAPTCHA |
| **Elevation of Privilege** | User → Admin | Full system compromise | Strict authorization checks |

---

## 2. Data Classification & Handling

### 2.1 Data Categories (GDPR Article 9)

| Category | Examples | Handling | Retention |
|----------|----------|----------|-----------|
| **Public** | Ticket location, status | Open data | 5 years |
| **Internal** | Operator notes | Role-based access | 5 years |
| **Confidential** | Reporter email, phone | Encrypted, strict need-to-know | 5 years or anonymized |
| **Sensitive** | Religious symbols in photos | Special handling, consent | Case-by-case |

---

## 3. Encryption Architecture

### 3.1 At-Rest Encryption

#### Database Field-Level Encryption

```php
<?php

namespace Modules\Fixcity\app\Traits;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

trait EncryptsAttributes
{
    /**
     * Attributes that should be encrypted at rest
     */
    protected array $encryptedAttributes = [];
    
    /**
     * Automatically encrypt on set
     */
    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->encryptedAttributes) && !is_null($value)) {
            $value = $this->encryptAttribute($value);
        }
        
        return parent::setAttribute($key, $value);
    }
    
    /**
     * Automatically decrypt on get
     */
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);
        
        if (in_array($key, $this->encryptedAttributes) && !is_null($value)) {
            try {
                return $this->decryptAttribute($value);
            } catch (DecryptException $e) {
                // Log security event
                logger()->error('Decryption failed', [
                    'model' => static::class,
                    'key' => $key,
                    'exception' => $e->getMessage()
                ]);
                return '[DECRYPTION_ERROR]';
            }
        }
        
        return $value;
    }
    
    /**
     * Encrypt using AES-256-GCM
     */
    protected function encryptAttribute($value): string
    {
        return Crypt::encryptString($value);
    }
    
    /**
     * Decrypt
     */
    protected function decryptAttribute($encrypted): string
    {
        return Crypt::decryptString($encrypted);
    }
    
    /**
     * Search encrypted fields (using hash for exact match)
     */
    public function scopeWhereEncrypted($query, $column, $value)
    {
        // Store blind index for searchable encrypted fields
        $blindIndexColumn = $column . '_index';
        $blindIndex = hash('sha256', $value);
        
        return $query->where($blindIndexColumn, $blindIndex);
    }
}
```

#### Ticket Model Implementation

```php
<?php

namespace Modules\Fixcity\app\Models;

use Modules\Fixcity\app\Traits\EncryptsAttributes;

class Ticket extends Model
{
    use EncryptsAttributes;
    
    /**
     * Fields encrypted at rest (AES-256-GCM)
     */
    protected array $encryptedAttributes = [
        'reporter_email',
        'reporter_phone',
        'reporter_name', // If anonymous reporting allowed
        'ip_address',    // Privacy protection
    ];
    
    /**
     * Blind indexes for searchable encrypted fields
     */
    protected array $blindIndexes = [
        'reporter_email' => 'reporter_email_index',
    ];
    
    protected static function boot()
    {
        parent::boot();
        
        // Generate blind indexes before save
        static::saving(function ($ticket) {
            foreach ($ticket->blindIndexes as $field => $indexField) {
                if ($ticket->isDirty($field) && !empty($ticket->$field)) {
                    $decrypted = $ticket->getOriginal($field) ?? $ticket->$field;
                    $ticket->$indexField = hash('sha256', strtolower(trim($decrypted)));
                }
            }
        });
    }
}
```

#### Migration for Blind Indexes

```php
<?php

Schema::table('tickets', function (Blueprint $table) {
    // Blind index for encrypted email search
    $table->string('reporter_email_index', 64)->nullable()->index();
    
    // Additional privacy fields
    $table->timestamp('anonymized_at')->nullable();
    $table->string('anonymization_reason', 50)->nullable();
});
```

---

### 3.2 In-Transit Encryption

#### TLS Configuration (nginx)

```nginx
# /etc/nginx/conf.d/fixcity-ssl.conf
server {
    listen 443 ssl http2;
    server_name fixcity.it;
    
    # Modern TLS 1.3 + 1.2 only
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384;
    ssl_prefer_server_ciphers off;
    
    # HSTS (Strict Transport Security)
    add_header Strict-Transport-Security "max-age=63072000; includeSubDomains; preload" always;
    
    # Certificate pinning info
    add_header Expect-CT "max-age=86400, enforce" always;
    
    # OCSP Stapling
    ssl_stapling on;
    ssl_stapling_verify on;
    ssl_trusted_certificate /etc/letsencrypt/chain.pem;
    
    # Certificate files
    ssl_certificate /etc/letsencrypt/live/fixcity.it/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/fixcity.it/privkey.pem;
    
    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    add_header Permissions-Policy "geolocation=(self), camera=(self), microphone=()" always;
    
    # Content Security Policy
    add_header Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data: https: blob:; connect-src 'self' https://api.fixcity.it; media-src 'self' blob:; object-src 'none'; frame-ancestors 'none'; base-uri 'self'; form-action 'self';" always;
}

# Redirect HTTP to HTTPS
server {
    listen 80;
    server_name fixcity.it;
    return 301 https://$server_name$request_uri;
}
```

---

## 4. GDPR Compliance Implementation

### 4.1 Legal Basis Mapping (Article 6)

| Processing | Legal Basis | Implementation |
|------------|-------------|----------------|
| Ticket creation | Art. 6(1)(a) - Consent | Explicit checkbox, record timestamp |
| Email notification | Art. 6(1)(a) - Consent | Opt-in during registration |
| Location data | Art. 6(1)(f) - Legitimate interest | Geofencing, no precise home address storage |
| Data retention | Art. 5(1)(e) - Storage limitation | Automatic anonymization after 5 years |
| Data sharing with comune | Art. 6(1)(e) - Public interest | DPA agreement with municipality |

---

### 4.2 Consent Management

```php
<?php

namespace Modules\Fixcity\app\Services\Gdpr;

class ConsentManager
{
    /**
     * Consent types required
     */
    public const CONSENT_TICKET_CREATION = 'ticket_creation';
    public const CONSENT_EMAIL_NOTIFICATION = 'email_notifications';
    public const CONSENT_LOCATION_PRECISE = 'precise_location';
    public const CONSENT_PUBLIC_DISPLAY = 'public_display';
    
    /**
     * Record granular consent
     */
    public function recordConsent(
        User $user,
        string $consentType,
        bool $granted,
        array $metadata = []
    ): ConsentRecord {
        return ConsentRecord::create([
            'user_id' => $user->id,
            'consent_type' => $consentType,
            'granted' => $granted,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'recorded_at' => now(),
            'expires_at' => $this->getConsentExpiry($consentType),
            'metadata' => json_encode($metadata),
            'privacy_policy_version' => config('fixcity.privacy_policy_version'),
        ]);
    }
    
    /**
     * Check if valid consent exists
     */
    public function hasValidConsent(User $user, string $consentType): bool
    {
        return ConsentRecord::where('user_id', $user->id)
            ->where('consent_type', $consentType)
            ->where('granted', true)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            ->exists();
    }
    
    /**
     * Withdraw consent (GDPR Article 7(3))
     */
    public function withdrawConsent(User $user, string $consentType): void
    {
        // Record withdrawal
        $this->recordConsent($user, $consentType, false, ['withdrawal' => true]);
        
        // Trigger related actions
        match($consentType) {
            self::CONSENT_EMAIL_NOTIFICATION => $this->disableNotifications($user),
            self::CONSENT_PUBLIC_DISPLAY => $this->anonymizePublicTickets($user),
            default => null
        };
        
        // Log for audit
        AuditLog::gdpr('consent_withdrawn', [
            'user_id' => $user->id,
            'consent_type' => $consentType,
        ]);
    }
    
    /**
     * Expiry periods per consent type
     */
    protected function getConsentExpiry(string $consentType): ?Carbon
    {
        return match($consentType) {
            self::CONSENT_TICKET_CREATION => now()->addYears(5),
            self::CONSENT_EMAIL_NOTIFICATION => now()->addYears(2),
            default => now()->addYear(),
        };
    }
}
```

#### Consent Database Schema

```sql
CREATE TABLE consent_records (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    consent_type VARCHAR(50) NOT NULL,
    granted BOOLEAN NOT NULL,
    ip_address INET NOT NULL,
    user_agent TEXT,
    privacy_policy_version VARCHAR(20) NOT NULL,
    recorded_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    expires_at TIMESTAMP WITH TIME ZONE,
    withdrawn_at TIMESTAMP WITH TIME ZONE,
    metadata JSONB,
    UNIQUE(user_id, consent_type, recorded_at)
);

CREATE INDEX idx_consent_user_type ON consent_records(user_id, consent_type, recorded_at DESC);
```

---

### 4.3 Right to Erasure (Article 17)

```php
<?php

namespace Modules\Fixcity\app\Services\Gdpr;

class DataErasureService
{
    /**
     * Execute right to erasure with exceptions
     */
    public function eraseUserData(User $user, bool $adminOverride = false): ErasureReport
    {
        $report = new ErasureReport();
        
        DB::transaction(function () use ($user, $report, $adminOverride) {
            // 1. Tickets (anonymize instead of delete for public records)
            $tickets = Ticket::where('reporter_id', $user->id)->get();
            foreach ($tickets as $ticket) {
                if ($ticket->is_public_record && !$adminOverride) {
                    // Anonymize but keep for public interest
                    $this->anonymizeTicket($ticket);
                    $report->anonymized('tickets', $ticket->id);
                } else {
                    // Hard delete if allowed
                    $ticket->forceDelete();
                    $report->deleted('tickets', $ticket->id);
                }
            }
            
            // 2. Comments
            Comment::where('author_id', $user->id)
                ->update([
                    'author_id' => null,
                    'content' => '[Content removed at user request]',
                    'content_html' => '<em>[Content removed]</em>',
                ]);
            $report->modified('comments', 'All user comments anonymized');
            
            // 3. Media uploaded by user
            $media = TicketMedia::where('uploaded_by', $user->id)->get();
            foreach ($media as $m) {
                Storage::delete([$m->path_original, $m->path_processed, $m->path_thumbnail]);
                $m->delete();
                $report->deleted('media', $m->id);
            }
            
            // 4. Personal data in user record
            $this->eraseUserRecord($user);
            $report->deleted('user', $user->id);
            
            // 5. Consent records (keep for legal compliance)
            ConsentRecord::where('user_id', $user->id)
                ->update(['user_id' => null]);
            $report->modified('consent_records', 'User reference removed');
            
            // 6. Audit logs (keep but anonymize)
            AuditLog::where('user_id', $user->id)
                ->update(['user_id' => null, 'user_identifier' => hash('sha256', $user->id)]);
            $report->modified('audit_logs', 'User reference anonymized');
        });
        
        // Send confirmation
        event(new DataErasureCompleted($user, $report));
        
        return $report;
    }
    
    /**
     * Anonymize ticket while preserving public record
     */
    protected function anonymizeTicket(Ticket $ticket): void
    {
        $ticket->update([
            'reporter_id' => null,
            'reporter_name' => 'Anonymous',
            'reporter_email' => null,
            'reporter_phone' => null,
            'reporter_email_index' => null,
            'ip_address' => null,
            'user_agent' => null,
            'anonymized_at' => now(),
            'anonymization_reason' => 'user_request_article_17',
        ]);
    }
    
    /**
     * Check if erasure is prohibited (legal obligations)
     */
    public function canErase(User $user): bool
    {
        // Check for ongoing legal proceedings
        if (Ticket::where('reporter_id', $user->id)
            ->where('legal_hold', true)
            ->exists()) {
            return false;
        }
        
        // Check admin status
        if ($user->is_admin) {
            return false; // Admin accounts need special handling
        }
        
        return true;
    }
}
```

---

### 4.4 Data Portability (Article 20)

```php
<?php

namespace Modules\Fixcity\app\Services\Gdpr;

class DataPortabilityService
{
    /**
     * Export user data in machine-readable format (JSON)
     */
    public function exportUserData(User $user): array
    {
        $export = [
            'export_metadata' => [
                'generated_at' => now()->toIso8601String(),
                'version' => '1.0',
                'format' => 'JSON',
            ],
            'user_profile' => [
                'id' => $user->uuid, // External ID only
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => $user->created_at,
                'verified_at' => $user->email_verified_at,
            ],
            'consent_history' => $this->exportConsents($user),
            'tickets' => $this->exportTickets($user),
            'activity' => $this->exportActivity($user),
        ];
        
        return $export;
    }
    
    /**
     * Export in structured CSV for tickets
     */
    public function exportTicketsCsv(User $user): string
    {
        $tickets = Ticket::where('reporter_id', $user->id)->get();
        
        $csv = Writer::createFromString('');
        $csv->insertOne([
            'ticket_uuid', 'title', 'description', 'type', 'status',
            'created_at', 'resolved_at', 'address', 'satisfaction_score'
        ]);
        
        foreach ($tickets as $ticket) {
            $csv->insertOne([
                $ticket->uuid,
                $ticket->title,
                $ticket->description,
                $ticket->type->value,
                $ticket->status->value,
                $ticket->created_at,
                $ticket->closed_at,
                $ticket->address,
                $ticket->survey_satisfaction_score,
            ]);
        }
        
        return $csv->getContent();
    }
}
```

---

### 4.5 Automatic Data Retention & Anonymization

```php
<?php

namespace Modules\Fixcity\app\Console\Commands;

class DataRetentionCommand extends Command
{
    protected $signature = 'gdpr:enforce-retention';
    
    public function handle(): void
    {
        // 1. Anonymize tickets older than 5 years
        $this->anonymizeOldTickets();
        
        // 2. Delete draft tickets older than 30 days
        $this->deleteOldDrafts();
        
        // 3. Purge soft-deleted records after 90 days
        $this->purgeSoftDeleted();
        
        // 4. Compress and archive audit logs older than 1 year
        $this->archiveAuditLogs();
    }
    
    protected function anonymizeOldTickets(): void
    {
        $cutoffDate = now()->subYears(5);
        
        Ticket::where('created_at', '<', $cutoffDate)
            ->whereNull('anonymized_at')
            ->chunkById(1000, function ($tickets) {
                foreach ($tickets as $ticket) {
                    app(DataErasureService::class)->anonymizeTicket($ticket);
                }
            });
    }
    
    protected function deleteOldDrafts(): void
    {
        Ticket::where('status', 'draft')
            ->where('updated_at', '<', now()->subDays(30))
            ->forceDelete();
    }
    
    protected function purgeSoftDeleted(): void
    {
        Ticket::onlyTrashed()
            ->where('deleted_at', '<', now()->subDays(90))
            ->forceDelete();
    }
}
```

**Cron schedule:**
```php
// routes/console.php
Schedule::command('gdpr:enforce-retention')->monthly();
```

---

## 5. Audit & Monitoring

### 5.1 Comprehensive Audit Logging

```php
<?php

namespace Modules\Fixcity\app\Services;

class AuditService
{
    /**
     * Security event types
     */
    public const EVENT_LOGIN = 'login';
    public const EVENT_LOGIN_FAILED = 'login_failed';
    public const EVENT_LOGOUT = 'logout';
    public const EVENT_PASSWORD_CHANGE = 'password_change';
    public const EVENT_DATA_EXPORT = 'data_export';
    public const EVENT_DATA_ERASURE = 'data_erasure';
    public const EVENT_CONSENT_WITHDRAWN = 'consent_withdrawn';
    public const EVENT_TICKET_CREATED = 'ticket_created';
    public const EVENT_TICKET_STATUS_CHANGED = 'ticket_status_changed';
    public const EVENT_TICKET_VIEWED = 'ticket_viewed';
    public const EVENT_ADMIN_ACCESS = 'admin_access';
    public const EVENT_PERMISSION_DENIED = 'permission_denied';
    public const EVENT_RATE_LIMIT_HIT = 'rate_limit_hit';
    
    /**
     * Log security event
     */
    public function log(
        string $event,
        ?User $user = null,
        array $context = [],
        string $severity = 'info'
    ): void {
        $log = SecurityAuditLog::create([
            'event' => $event,
            'user_id' => $user?->id,
            'user_identifier' => $this->getUserIdentifier($user),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'context' => json_encode($context),
            'severity' => $severity,
            'occurred_at' => now(),
            'session_id' => session()->getId(),
            'request_id' => request()->header('X-Request-ID') ?: uniqid(),
        ]);
        
        // Real-time alerting for critical events
        if (in_array($event, $this->criticalEvents())) {
            $this->alertSecurityTeam($log);
        }
    }
    
    /**
     * Detect suspicious patterns
     */
    public function detectAnomalies(): array
    {
        $alerts = [];
        
        // 1. Multiple failed logins
        $failedLogins = SecurityAuditLog::where('event', self::EVENT_LOGIN_FAILED)
            ->where('occurred_at', '>', now()->subHour())
            ->select('ip_address', DB::raw('count(*) as count'))
            ->groupBy('ip_address')
            ->having('count', '>', 10)
            ->get();
            
        foreach ($failedLogins as $record) {
            $alerts[] = [
                'type' => 'brute_force_attack',
                'severity' => 'high',
                'ip_address' => $record->ip_address,
                'count' => $record->count,
            ];
        }
        
        // 2. Unusual access patterns (outside business hours)
        $afterHoursAccess = SecurityAuditLog::where('event', self::EVENT_ADMIN_ACCESS)
            ->where('occurred_at', '>', now()->subDay())
            ->whereRaw("EXTRACT(HOUR FROM occurred_at) NOT BETWEEN 8 AND 20")
            ->whereRaw("EXTRACT(DOW FROM occurred_at) IN (0, 6)") // Weekend
            ->count();
            
        if ($afterHoursAccess > 50) {
            $alerts[] = [
                'type' => 'unusual_after_hours_access',
                'severity' => 'medium',
                'count' => $afterHoursAccess,
            ];
        }
        
        // 3. Mass data export
        $exports = SecurityAuditLog::where('event', self::EVENT_DATA_EXPORT)
            ->where('occurred_at', '>', now()->subHour())
            ->count();
            
        if ($exports > 10) {
            $alerts[] = [
                'type' => 'mass_data_export',
                'severity' => 'critical',
                'count' => $exports,
            ];
        }
        
        return $alerts;
    }
    
    protected function criticalEvents(): array
    {
        return [
            self::EVENT_DATA_ERASURE,
            self::EVENT_ADMIN_ACCESS,
            self::EVENT_PERMISSION_DENIED,
            self::EVENT_RATE_LIMIT_HIT,
        ];
    }
}
```

---

## 6. Incident Response

### 6.1 Data Breach Response Plan

```php
<?php

namespace Modules\Fixcity\app\Services\Security;

class IncidentResponseService
{
    /**
     * Data breach notification (GDPR Article 33/34)
     */
    public function handleDataBreach(
        string $type,
        array $affectedUsers,
        string $description,
        Carbon $occurredAt
    ): void {
        // 1. Log incident
        $incident = SecurityIncident::create([
            'type' => $type,
            'severity' => $this->assessSeverity($type, count($affectedUsers)),
            'description' => $description,
            'affected_users_count' => count($affectedUsers),
            'occurred_at' => $occurredAt,
            'detected_at' => now(),
            'status' => 'detected',
        ]);
        
        // 2. Immediate containment
        $this->executeContainment($type);
        
        // 3. Notify DPA (within 72 hours if high risk)
        if ($incident->severity === 'high' || $incident->severity === 'critical') {
            $this->notifyDataProtectionAuthority($incident);
        }
        
        // 4. Notify affected users (if high risk)
        if ($incident->severity === 'critical') {
            foreach ($affectedUsers as $user) {
                $this->notifyUserOfBreach($user, $incident);
            }
        }
        
        // 5. Document everything for investigation
        $this->preserveEvidence($incident);
    }
    
    /**
     * Assess severity based on data types and volume
     */
    protected function assessSeverity(string $type, int $count): string
    {
        return match($type) {
            'unauthorized_access_admin' => 'critical',
            'data_export_unauthorized' => 'high',
            'personal_data_exposure' => 'critical',
            'credentials_leak' => 'critical',
            'dos_attack' => 'medium',
            default => 'low',
        };
    }
}
```

---

*Security architecture based on: OWASP guidelines, GDPR Article 32 (security of processing), ISO 27001, NIST Cybersecurity Framework.*
