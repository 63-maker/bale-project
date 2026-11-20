-- ============================================================================
-- COLLEGE COMPLAINT MANAGEMENT SYSTEM (CCMS)
-- SUPABASE ROW LEVEL SECURITY (RLS) & ACCESS CONTROL POLICIES
-- ============================================================================

-- Helper functions for session user & role in transactional context
CREATE OR REPLACE FUNCTION current_app_user_id() RETURNS uuid AS $$
BEGIN
    RETURN NULLIF(current_setting('app.user_id', true), '')::uuid;
EXCEPTION WHEN OTHERS THEN
    RETURN NULL;
END;
$$ LANGUAGE plpgsql STABLE;

CREATE OR REPLACE FUNCTION current_app_user_role() RETURNS varchar AS $$
DECLARE
    u_role varchar;
BEGIN
    SELECT role INTO u_role FROM profiles WHERE id = current_app_user_id();
    RETURN u_role;
EXCEPTION WHEN OTHERS THEN
    RETURN NULL;
END;
$$ LANGUAGE plpgsql STABLE SECURITY DEFINER;

-- Enable RLS on all 12 tables
ALTER TABLE profiles ENABLE ROW LEVEL SECURITY;
ALTER TABLE departments ENABLE ROW LEVEL SECURITY;
ALTER TABLE complaint_categories ENABLE ROW LEVEL SECURITY;
ALTER TABLE complaints ENABLE ROW LEVEL SECURITY;
ALTER TABLE complaint_assignments ENABLE ROW LEVEL SECURITY;
ALTER TABLE complaint_comments ENABLE ROW LEVEL SECURITY;
ALTER TABLE complaint_attachments ENABLE ROW LEVEL SECURITY;
ALTER TABLE complaint_status_history ENABLE ROW LEVEL SECURITY;
ALTER TABLE complaint_verifications ENABLE ROW LEVEL SECURITY;
ALTER TABLE notifications ENABLE ROW LEVEL SECURITY;
ALTER TABLE audit_logs ENABLE ROW LEVEL SECURITY;
ALTER TABLE system_settings ENABLE ROW LEVEL SECURITY;

-- 1. PROFILES POLICIES
CREATE POLICY p_profiles_admin ON profiles
    FOR ALL USING (current_app_user_role() = 'admin');

CREATE POLICY p_profiles_select_self ON profiles
    FOR SELECT USING (id = current_app_user_id() OR current_app_user_role() = 'admin');

CREATE POLICY p_profiles_update_self ON profiles
    FOR UPDATE USING (id = current_app_user_id())
    WITH CHECK (id = current_app_user_id());

-- 2. DEPARTMENTS POLICIES
CREATE POLICY p_departments_select ON departments
    FOR SELECT USING (true);

CREATE POLICY p_departments_admin ON departments
    FOR ALL USING (current_app_user_role() = 'admin');

-- 3. COMPLAINT CATEGORIES POLICIES
CREATE POLICY p_categories_select ON complaint_categories
    FOR SELECT USING (true);

CREATE POLICY p_categories_admin ON complaint_categories
    FOR ALL USING (current_app_user_role() = 'admin');

-- 4. COMPLAINTS POLICIES
CREATE POLICY p_complaints_admin ON complaints
    FOR ALL USING (current_app_user_role() = 'admin');

CREATE POLICY p_complaints_student_select ON complaints
    FOR SELECT USING (
        student_id = current_app_user_id() OR
        EXISTS (
            SELECT 1 FROM complaint_assignments
            WHERE complaint_assignments.complaint_id = complaints.id
            AND complaint_assignments.management_id = current_app_user_id()
            AND complaint_assignments.is_active = true
        )
    );

CREATE POLICY p_complaints_student_insert ON complaints
    FOR INSERT WITH CHECK (
        student_id = current_app_user_id() AND
        current_app_user_role() = 'student'
    );

-- 5. COMPLAINT ASSIGNMENTS POLICIES
CREATE POLICY p_assignments_admin ON complaint_assignments
    FOR ALL USING (current_app_user_role() = 'admin');

CREATE POLICY p_assignments_management_select ON complaint_assignments
    FOR SELECT USING (management_id = current_app_user_id());

-- 6. COMPLAINT COMMENTS POLICIES
CREATE POLICY p_comments_admin ON complaint_comments
    FOR ALL USING (current_app_user_role() = 'admin');

CREATE POLICY p_comments_select ON complaint_comments
    FOR SELECT USING (
        (visibility = 'public' AND EXISTS (
            SELECT 1 FROM complaints WHERE complaints.id = complaint_comments.complaint_id AND complaints.student_id = current_app_user_id()
        )) OR
        EXISTS (
            SELECT 1 FROM complaint_assignments
            WHERE complaint_assignments.complaint_id = complaint_comments.complaint_id
            AND complaint_assignments.management_id = current_app_user_id()
            AND complaint_assignments.is_active = true
        )
    );

-- 7. COMPLAINT ATTACHMENTS POLICIES
CREATE POLICY p_attachments_admin ON complaint_attachments
    FOR ALL USING (current_app_user_role() = 'admin');

CREATE POLICY p_attachments_select ON complaint_attachments
    FOR SELECT USING (
        EXISTS (SELECT 1 FROM complaints WHERE complaints.id = complaint_attachments.complaint_id AND complaints.student_id = current_app_user_id()) OR
        EXISTS (
            SELECT 1 FROM complaint_assignments
            WHERE complaint_assignments.complaint_id = complaint_attachments.complaint_id
            AND complaint_assignments.management_id = current_app_user_id()
            AND complaint_assignments.is_active = true
        )
    );

-- 8. COMPLAINT STATUS HISTORY POLICIES
CREATE POLICY p_history_admin ON complaint_status_history
    FOR ALL USING (current_app_user_role() = 'admin');

CREATE POLICY p_history_select ON complaint_status_history
    FOR SELECT USING (
        EXISTS (SELECT 1 FROM complaints WHERE complaints.id = complaint_status_history.complaint_id AND complaints.student_id = current_app_user_id()) OR
        EXISTS (
            SELECT 1 FROM complaint_assignments
            WHERE complaint_assignments.complaint_id = complaint_status_history.complaint_id
            AND complaint_assignments.management_id = current_app_user_id()
            AND complaint_assignments.is_active = true
        )
    );

-- 9. COMPLAINT VERIFICATIONS POLICIES
CREATE POLICY p_verifications_admin ON complaint_verifications
    FOR ALL USING (current_app_user_role() = 'admin');

CREATE POLICY p_verifications_select ON complaint_verifications
    FOR SELECT USING (
        management_id = current_app_user_id() OR
        EXISTS (SELECT 1 FROM complaints WHERE complaints.id = complaint_verifications.complaint_id AND complaints.student_id = current_app_user_id())
    );

-- 10. NOTIFICATIONS POLICIES
CREATE POLICY p_notifications_self ON notifications
    FOR ALL USING (user_id = current_app_user_id());

-- 11. AUDIT LOGS POLICIES
CREATE POLICY p_audit_logs_admin ON audit_logs
    FOR SELECT USING (current_app_user_role() = 'admin');

-- 12. SYSTEM SETTINGS POLICIES
CREATE POLICY p_settings_admin ON system_settings
    FOR ALL USING (current_app_user_role() = 'admin');

CREATE POLICY p_settings_authenticated_select ON system_settings
    FOR SELECT USING (current_app_user_id() IS NOT NULL);
