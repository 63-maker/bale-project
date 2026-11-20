-- ============================================================================
-- COLLEGE COMPLAINT MANAGEMENT SYSTEM (CCMS)
-- SUPABASE POSTGRESQL PRODUCTION INITIAL SCHEMA
-- ============================================================================

CREATE EXTENSION IF NOT EXISTS "pgcrypto";

-- 1. DEPARTMENTS
CREATE TABLE IF NOT EXISTS departments (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    name VARCHAR(150) NOT NULL,
    code VARCHAR(20) NOT NULL UNIQUE,
    is_active BOOLEAN NOT NULL DEFAULT true,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- 2. PROFILES
CREATE TABLE IF NOT EXISTS profiles (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    register_number VARCHAR(50) UNIQUE NULL,
    employee_id VARCHAR(50) UNIQUE NULL,
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL CHECK (role IN ('student', 'management', 'admin')),
    department_id UUID NULL REFERENCES departments(id) ON DELETE SET NULL,
    study_year SMALLINT NULL CHECK (study_year BETWEEN 1 AND 5),
    account_status VARCHAR(20) NOT NULL DEFAULT 'active' CHECK (account_status IN ('active', 'suspended')),
    must_change_password BOOLEAN NOT NULL DEFAULT false,
    email_verified_at TIMESTAMPTZ NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    CONSTRAINT chk_student_fields CHECK (
        (role = 'student' AND register_number IS NOT NULL AND study_year IS NOT NULL AND department_id IS NOT NULL) OR
        (role IN ('management', 'admin'))
    ),
    CONSTRAINT chk_management_fields CHECK (
        (role = 'management' AND employee_id IS NOT NULL AND department_id IS NOT NULL) OR
        (role IN ('student', 'admin'))
    )
);

CREATE INDEX IF NOT EXISTS idx_profiles_role_status ON profiles(role, account_status);
CREATE INDEX IF NOT EXISTS idx_profiles_department ON profiles(department_id);

-- 3. COMPLAINT CATEGORIES
CREATE TABLE IF NOT EXISTS complaint_categories (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    name VARCHAR(100) NOT NULL,
    description TEXT NULL,
    department_id UUID NULL REFERENCES departments(id) ON DELETE SET NULL,
    is_active BOOLEAN NOT NULL DEFAULT true,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_categories_dept ON complaint_categories(department_id);

-- 4. COMPLAINT COUNTERS (FOR TRANSACTIONAL NUMBER GENERATION)
CREATE TABLE IF NOT EXISTS complaint_counters (
    year INT PRIMARY KEY,
    last_number BIGINT NOT NULL DEFAULT 0,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- 5. COMPLAINTS
CREATE TABLE IF NOT EXISTS complaints (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    complaint_number VARCHAR(50) NOT NULL UNIQUE,
    student_id UUID NOT NULL REFERENCES profiles(id) ON DELETE CASCADE,
    category_id UUID NOT NULL REFERENCES complaint_categories(id) ON DELETE RESTRICT,
    department_id UUID NOT NULL REFERENCES departments(id) ON DELETE RESTRICT,
    subject VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    priority VARCHAR(20) NOT NULL DEFAULT 'MEDIUM' CHECK (priority IN ('LOW', 'MEDIUM', 'HIGH', 'CRITICAL')),
    status VARCHAR(30) NOT NULL DEFAULT 'SUBMITTED' CHECK (status IN (
        'SUBMITTED', 'UNDER_REVIEW', 'ASSIGNED', 'IN_PROGRESS', 
        'RESOLUTION_SUBMITTED', 'VERIFICATION_REJECTED', 'VERIFIED_FOR_CLOSURE', 
        'CLOSED', 'REOPENED', 'REJECTED'
    )),
    resolution_description TEXT NULL,
    resolution_submitted_at TIMESTAMPTZ NULL,
    verified_by UUID NULL REFERENCES profiles(id) ON DELETE SET NULL,
    verified_at TIMESTAMPTZ NULL,
    verification_remarks TEXT NULL,
    closed_by UUID NULL REFERENCES profiles(id) ON DELETE SET NULL,
    closed_at TIMESTAMPTZ NULL,
    rejection_reason TEXT NULL,
    reopened_reason TEXT NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_complaints_status_priority ON complaints(status, priority);
CREATE INDEX IF NOT EXISTS idx_complaints_student ON complaints(student_id, status);
CREATE INDEX IF NOT EXISTS idx_complaints_department ON complaints(department_id, status);

-- 6. COMPLAINT ASSIGNMENTS
CREATE TABLE IF NOT EXISTS complaint_assignments (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    complaint_id UUID NOT NULL REFERENCES complaints(id) ON DELETE CASCADE,
    management_id UUID NOT NULL REFERENCES profiles(id) ON DELETE CASCADE,
    assigned_by UUID NOT NULL REFERENCES profiles(id) ON DELETE CASCADE,
    assignment_method VARCHAR(20) NOT NULL DEFAULT 'manual' CHECK (assignment_method IN ('manual', 'automatic')),
    is_active BOOLEAN NOT NULL DEFAULT true,
    assigned_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    unassigned_at TIMESTAMPTZ NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_assignments_mgmt ON complaint_assignments(management_id, is_active);
CREATE INDEX IF NOT EXISTS idx_assignments_complaint ON complaint_assignments(complaint_id, is_active);

-- 7. COMPLAINT COMMENTS
CREATE TABLE IF NOT EXISTS complaint_comments (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    complaint_id UUID NOT NULL REFERENCES complaints(id) ON DELETE CASCADE,
    sender_id UUID NOT NULL REFERENCES profiles(id) ON DELETE CASCADE,
    message TEXT NOT NULL,
    visibility VARCHAR(20) NOT NULL DEFAULT 'public' CHECK (visibility IN ('public', 'internal')),
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_comments_complaint ON complaint_comments(complaint_id, visibility);

-- 8. COMPLAINT ATTACHMENTS
CREATE TABLE IF NOT EXISTS complaint_attachments (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    complaint_id UUID NOT NULL REFERENCES complaints(id) ON DELETE CASCADE,
    uploaded_by UUID NOT NULL REFERENCES profiles(id) ON DELETE CASCADE,
    storage_path VARCHAR(500) NOT NULL,
    original_filename VARCHAR(255) NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    file_size BIGINT NOT NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_attachments_complaint ON complaint_attachments(complaint_id);

-- 9. COMPLAINT STATUS HISTORY
CREATE TABLE IF NOT EXISTS complaint_status_history (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    complaint_id UUID NOT NULL REFERENCES complaints(id) ON DELETE CASCADE,
    previous_status VARCHAR(30) NULL,
    new_status VARCHAR(30) NOT NULL,
    changed_by UUID NULL REFERENCES profiles(id) ON DELETE SET NULL,
    remarks TEXT NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_status_history_complaint ON complaint_status_history(complaint_id);

-- 10. COMPLAINT VERIFICATIONS
CREATE TABLE IF NOT EXISTS complaint_verifications (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    complaint_id UUID NOT NULL REFERENCES complaints(id) ON DELETE CASCADE,
    management_id UUID NOT NULL REFERENCES profiles(id) ON DELETE CASCADE,
    admin_id UUID NOT NULL REFERENCES profiles(id) ON DELETE CASCADE,
    decision VARCHAR(20) NOT NULL CHECK (decision IN ('approved', 'rejected')),
    remarks TEXT NOT NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_verifications_complaint ON complaint_verifications(complaint_id);

-- 11. NOTIFICATIONS
CREATE TABLE IF NOT EXISTS notifications (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID NOT NULL REFERENCES profiles(id) ON DELETE CASCADE,
    complaint_id UUID NULL REFERENCES complaints(id) ON DELETE CASCADE,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN NOT NULL DEFAULT false,
    read_at TIMESTAMPTZ NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_notifications_user_unread ON notifications(user_id, is_read);

-- 12. AUDIT LOGS
CREATE TABLE IF NOT EXISTS audit_logs (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    actor_id UUID NULL REFERENCES profiles(id) ON DELETE SET NULL,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(100) NOT NULL,
    entity_id VARCHAR(100) NULL,
    details JSONB NULL,
    ip_address VARCHAR(45) NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_audit_logs_actor ON audit_logs(actor_id, created_at);
CREATE INDEX IF NOT EXISTS idx_audit_logs_entity ON audit_logs(entity_type, entity_id);

-- 13. SYSTEM SETTINGS
CREATE TABLE IF NOT EXISTS system_settings (
    setting_key VARCHAR(100) PRIMARY KEY,
    setting_value JSONB NOT NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);
