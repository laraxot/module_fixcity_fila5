-- ============================================
-- FIXCITY COMPLETE DATABASE SCHEMA
-- Version: 1.0.0
-- ============================================

-- ============================================
-- TICKETS CORE
-- ============================================

CREATE TABLE tickets (
    id BIGSERIAL PRIMARY KEY,
    uuid UUID DEFAULT gen_random_uuid() NOT NULL UNIQUE,
    
    -- Reporter info (denormalized for performance)
    reporter_type VARCHAR(20) DEFAULT 'citizen' CHECK (reporter_type IN ('citizen', 'anonymous', 'system')),
    reporter_id BIGINT REFERENCES users(id) ON DELETE SET NULL,
    reporter_name VARCHAR(255),
    reporter_email VARCHAR(255),
    reporter_phone VARCHAR(50),
    reporter_consent BOOLEAN DEFAULT FALSE,
    
    -- Content
    title VARCHAR(255) NOT NULL,
    description TEXT,
    type VARCHAR(50) NOT NULL,
    priority_score INTEGER DEFAULT 50 CHECK (priority_score BETWEEN 0 AND 100),
    urgency_level VARCHAR(20) DEFAULT 'medium' CHECK (urgency_level IN ('low', 'medium', 'high', 'critical')),
    
    -- Location (PostGIS)
    location GEOGRAPHY(POINT, 4326) NOT NULL,
    address TEXT,
    address_normalized TEXT,
    zone VARCHAR(100),
    city VARCHAR(100),
    postal_code VARCHAR(20),
    
    -- Status & Workflow
    status VARCHAR(50) DEFAULT 'submitted',
    
    -- Timeline timestamps
    reported_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    acknowledged_at TIMESTAMP WITH TIME ZONE,
    acknowledged_by BIGINT REFERENCES users(id) ON DELETE SET NULL,
    assigned_at TIMESTAMP WITH TIME ZONE,
    assigned_by BIGINT REFERENCES users(id) ON DELETE SET NULL,
    assigned_to BIGINT REFERENCES users(id) ON DELETE SET NULL,
    started_at TIMESTAMP WITH TIME ZONE,
    started_by BIGINT REFERENCES users(id) ON DELETE SET NULL,
    completed_at TIMESTAMP WITH TIME ZONE,
    completed_by BIGINT REFERENCES users(id) ON DELETE SET NULL,
    verified_at TIMESTAMP WITH TIME ZONE,
    verified_by BIGINT REFERENCES users(id) ON DELETE SET NULL,
    closed_at TIMESTAMP WITH TIME ZONE,
    closed_by BIGINT REFERENCES users(id) ON DELETE SET NULL,
    rejected_at TIMESTAMP WITH TIME ZONE,
    rejected_by BIGINT REFERENCES users(id) ON DELETE SET NULL,
    rejection_reason TEXT,
    
    -- SLA tracking
    sla_target_acknowledge_hours INTEGER DEFAULT 24,
    sla_target_resolve_hours INTEGER DEFAULT 168,
    sla_breached_acknowledge BOOLEAN DEFAULT FALSE,
    sla_breached_resolve BOOLEAN DEFAULT FALSE,
    
    -- Duplicate detection
    is_duplicate BOOLEAN DEFAULT FALSE,
    duplicate_of_id BIGINT REFERENCES tickets(id) ON DELETE SET NULL,
    duplicate_confidence_score DECIMAL(3,2),
    
    -- Metadata
    source VARCHAR(50) DEFAULT 'web',
    language VARCHAR(10) DEFAULT 'it',
    ip_address INET,
    user_agent TEXT,
    
    -- Survey
    survey_sent_at TIMESTAMP WITH TIME ZONE,
    survey_completed_at TIMESTAMP WITH TIME ZONE,
    survey_satisfaction_score INTEGER CHECK (survey_satisfaction_score BETWEEN 1 AND 5),
    survey_comment TEXT,
    
    -- Soft delete
    deleted_at TIMESTAMP WITH TIME ZONE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Indexes
CREATE INDEX idx_tickets_status ON tickets(status);
CREATE INDEX idx_tickets_type ON tickets(type);
CREATE INDEX idx_tickets_location ON tickets USING GIST(location);
CREATE INDEX idx_tickets_created_at ON tickets(created_at DESC);
CREATE INDEX idx_tickets_reported_at ON tickets(reported_at DESC);
CREATE INDEX idx_tickets_status_created ON tickets(status, created_at);
CREATE INDEX idx_tickets_assigned_to ON tickets(assigned_to) WHERE assigned_to IS NOT NULL;
CREATE INDEX idx_tickets_zone ON tickets(zone);
CREATE INDEX idx_tickets_duplicate_of ON tickets(duplicate_of_id) WHERE is_duplicate = TRUE;
CREATE INDEX idx_tickets_priority ON tickets(priority_score DESC, urgency_level);

-- Full-text search
CREATE INDEX idx_tickets_fulltext ON tickets 
    USING GIN(to_tsvector('italian', coalesce(title, '') || ' ' || coalesce(description, '')));

-- ============================================
-- TICKET STATUS HISTORY
-- ============================================

CREATE TABLE ticket_status_history (
    id BIGSERIAL PRIMARY KEY,
    ticket_id BIGINT NOT NULL REFERENCES tickets(id) ON DELETE CASCADE,
    from_status VARCHAR(50) NOT NULL,
    to_status VARCHAR(50) NOT NULL,
    changed_by BIGINT REFERENCES users(id) ON DELETE SET NULL,
    changed_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    notes TEXT,
    metadata JSONB,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

CREATE INDEX idx_ticket_status_history_ticket ON ticket_status_history(ticket_id, changed_at DESC);
CREATE INDEX idx_ticket_status_history_changed_by ON ticket_status_history(changed_by);

-- ============================================
-- QUESTIONNAIRES
-- ============================================

CREATE TABLE questionnaires (
    id BIGSERIAL PRIMARY KEY,
    ticket_type VARCHAR(50) NOT NULL,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    is_default BOOLEAN DEFAULT FALSE,
    version INTEGER DEFAULT 1,
    scoring_rules JSONB,
    created_by BIGINT REFERENCES users(id),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

CREATE UNIQUE INDEX idx_questionnaires_type_default ON questionnaires(ticket_type) WHERE is_default = TRUE;

-- ============================================
-- QUESTIONNAIRE FIELDS
-- ============================================

CREATE TABLE questionnaire_fields (
    id BIGSERIAL PRIMARY KEY,
    questionnaire_id BIGINT NOT NULL REFERENCES questionnaires(id) ON DELETE CASCADE,
    key VARCHAR(100) NOT NULL,
    label VARCHAR(255) NOT NULL,
    type VARCHAR(50) NOT NULL,
    is_required BOOLEAN DEFAULT FALSE,
    sort_order INTEGER DEFAULT 0,
    options JSONB,
    validation_rules JSONB,
    conditional_logic JSONB,
    ui_config JSONB,
    priority_impact VARCHAR(20) DEFAULT 'none',
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    UNIQUE(questionnaire_id, key)
);

CREATE INDEX idx_questionnaire_fields_questionnaire ON questionnaire_fields(questionnaire_id, sort_order);

-- ============================================
-- TICKET ANSWERS
-- ============================================

CREATE TABLE ticket_answers (
    id BIGSERIAL PRIMARY KEY,
    ticket_id BIGINT NOT NULL REFERENCES tickets(id) ON DELETE CASCADE,
    questionnaire_field_id BIGINT NOT NULL REFERENCES questionnaire_fields(id),
    value JSONB NOT NULL,
    display_value TEXT,
    priority_contribution INTEGER,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    UNIQUE(ticket_id, questionnaire_field_id)
);

CREATE INDEX idx_ticket_answers_ticket ON ticket_answers(ticket_id);

-- ============================================
-- MEDIA
-- ============================================

CREATE TABLE ticket_media (
    id BIGSERIAL PRIMARY KEY,
    ticket_id BIGINT NOT NULL REFERENCES tickets(id) ON DELETE CASCADE,
    type VARCHAR(20) CHECK (type IN ('image', 'video', 'audio', 'document')),
    original_filename VARCHAR(255),
    mime_type VARCHAR(100),
    file_size_bytes BIGINT,
    path_original VARCHAR(500),
    path_processed VARCHAR(500),
    path_thumbnail VARCHAR(500),
    width INTEGER,
    height INTEGER,
    duration_seconds INTEGER,
    perceptual_hash VARCHAR(64),
    blurhash VARCHAR(100),
    exif_data JSONB,
    has_gps BOOLEAN DEFAULT FALSE,
    gps_latitude DECIMAL(10,8),
    gps_longitude DECIMAL(11,8),
    gps_taken_at TIMESTAMP WITH TIME ZONE,
    uploaded_by BIGINT REFERENCES users(id),
    uploaded_from_ip INET,
    processed_at TIMESTAMP WITH TIME ZONE,
    processing_error TEXT,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

CREATE INDEX idx_ticket_media_ticket ON ticket_media(ticket_id);
CREATE INDEX idx_ticket_media_hash ON ticket_media(perceptual_hash) WHERE perceptual_hash IS NOT NULL;
CREATE INDEX idx_ticket_media_gps ON ticket_media(gps_latitude, gps_longitude) WHERE has_gps = TRUE;

-- ============================================
-- COMMENTS
-- ============================================

CREATE TABLE ticket_comments (
    id BIGSERIAL PRIMARY KEY,
    ticket_id BIGINT NOT NULL REFERENCES tickets(id) ON DELETE CASCADE,
    author_type VARCHAR(20),
    author_id BIGINT REFERENCES users(id) ON DELETE SET NULL,
    content TEXT NOT NULL,
    content_html TEXT,
    is_internal BOOLEAN DEFAULT FALSE,
    is_system_generated BOOLEAN DEFAULT FALSE,
    system_event_type VARCHAR(50),
    system_metadata JSONB,
    attachments JSONB,
    edited_at TIMESTAMP WITH TIME ZONE,
    edited_by BIGINT REFERENCES users(id),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

CREATE INDEX idx_ticket_comments_ticket ON ticket_comments(ticket_id, created_at DESC);
CREATE INDEX idx_ticket_comments_internal ON ticket_comments(ticket_id) WHERE is_internal = TRUE;

-- ============================================
-- NOTIFICATIONS
-- ============================================

CREATE TABLE notifications (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    type VARCHAR(100) NOT NULL,
    notifiable_type VARCHAR(50) NOT NULL,
    notifiable_id BIGINT NOT NULL,
    title VARCHAR(255),
    body TEXT,
    action_url TEXT,
    data JSONB,
    channel VARCHAR(50),
    sent_at TIMESTAMP WITH TIME ZONE,
    delivered_at TIMESTAMP WITH TIME ZONE,
    opened_at TIMESTAMP WITH TIME ZONE,
    clicked_at TIMESTAMP WITH TIME ZONE,
    failed_at TIMESTAMP WITH TIME ZONE,
    failure_reason TEXT,
    read_at TIMESTAMP WITH TIME ZONE,
    dismissed_at TIMESTAMP WITH TIME ZONE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

CREATE INDEX idx_notifications_notifiable ON notifications(notifiable_type, notifiable_id, read_at);
CREATE INDEX idx_notifications_type ON notifications(type, created_at DESC);

-- ============================================
-- STATISTICS
-- ============================================

CREATE TABLE ticket_statistics_hourly (
    id BIGSERIAL PRIMARY KEY,
    hour TIMESTAMP WITH TIME ZONE NOT NULL,
    ticket_type VARCHAR(50),
    status VARCHAR(50),
    zone VARCHAR(100),
    assigned_to BIGINT REFERENCES users(id),
    count INTEGER DEFAULT 0,
    avg_resolution_minutes INTEGER,
    sla_breach_count INTEGER DEFAULT 0,
    UNIQUE(hour, ticket_type, status, zone, assigned_to)
);

CREATE INDEX idx_stats_hourly_hour ON ticket_statistics_hourly(hour DESC);
CREATE INDEX idx_stats_hourly_type ON ticket_statistics_hourly(ticket_type, hour);

-- ============================================
-- DUPLICATE DETECTION LOGS
-- ============================================

CREATE TABLE duplicate_detection_logs (
    id BIGSERIAL PRIMARY KEY,
    ticket_id BIGINT NOT NULL REFERENCES tickets(id),
    candidate_ticket_id BIGINT REFERENCES tickets(id),
    similarity_score DECIMAL(3,2),
    factors JSONB,
    is_duplicate BOOLEAN,
    decided_by BIGINT REFERENCES users(id),
    decided_at TIMESTAMP WITH TIME ZONE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

CREATE INDEX idx_duplicate_logs_ticket ON duplicate_detection_logs(ticket_id);
CREATE INDEX idx_duplicate_logs_score ON duplicate_detection_logs(similarity_score DESC);
