<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Only execute PostgreSQL specific RLS and trigger statements on pgsql driver
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('CREATE EXTENSION IF NOT EXISTS pgcrypto;');

        // Enable Row Level Security on all 12 core tables
        $tables = [
            'profiles',
            'departments',
            'complaint_categories',
            'complaints',
            'complaint_assignments',
            'complaint_comments',
            'complaint_attachments',
            'complaint_status_history',
            'complaint_verifications',
            'notifications',
            'audit_logs',
            'system_settings',
        ];

        foreach ($tables as $table) {
            DB::statement("ALTER TABLE {$table} ENABLE ROW LEVEL SECURITY;");
        }

        // Helper function for current authenticated user in transaction
        DB::statement("
            CREATE OR REPLACE FUNCTION current_app_user_id() RETURNS uuid AS $$
            BEGIN
                RETURN NULLIF(current_setting('app.user_id', true), '')::uuid;
            EXCEPTION WHEN OTHERS THEN
                RETURN NULL;
            END;
            $$ LANGUAGE plpgsql STABLE;
        ");

        DB::statement('
            CREATE OR REPLACE FUNCTION current_app_user_role() RETURNS varchar AS $$
            DECLARE
                user_role varchar;
            BEGIN
                SELECT role INTO user_role FROM profiles WHERE id = current_app_user_id();
                RETURN user_role;
            EXCEPTION WHEN OTHERS THEN
                RETURN NULL;
            END;
            $$ LANGUAGE plpgsql STABLE SECURITY DEFINER;
        ');

        // Profiles RLS Policies
        DB::statement("
            CREATE POLICY profiles_admin_all ON profiles
            FOR ALL USING (current_app_user_role() = 'admin');

            CREATE POLICY profiles_self_read ON profiles
            FOR SELECT USING (id = current_app_user_id() OR current_app_user_role() = 'admin');

            CREATE POLICY profiles_self_update ON profiles
            FOR UPDATE USING (id = current_app_user_id());
        ");

        // Departments RLS Policies
        DB::statement("
            CREATE POLICY departments_read_all ON departments
            FOR SELECT USING (true);

            CREATE POLICY departments_admin_modify ON departments
            FOR ALL USING (current_app_user_role() = 'admin');
        ");

        // Complaint Categories RLS Policies
        DB::statement("
            CREATE POLICY categories_read_all ON complaint_categories
            FOR SELECT USING (true);

            CREATE POLICY categories_admin_modify ON complaint_categories
            FOR ALL USING (current_app_user_role() = 'admin');
        ");

        // Complaints RLS Policies
        DB::statement("
            CREATE POLICY complaints_admin_all ON complaints
            FOR ALL USING (current_app_user_role() = 'admin');

            CREATE POLICY complaints_student_read ON complaints
            FOR SELECT USING (
                student_id = current_app_user_id() OR
                current_app_user_role() = 'admin' OR
                EXISTS (
                    SELECT 1 FROM complaint_assignments
                    WHERE complaint_assignments.complaint_id = complaints.id
                    AND complaint_assignments.management_id = current_app_user_id()
                    AND complaint_assignments.is_active = true
                )
            );

            CREATE POLICY complaints_student_insert ON complaints
            FOR INSERT WITH CHECK (
                student_id = current_app_user_id() AND
                current_app_user_role() = 'student'
            );
        ");

        // Assignments RLS Policies
        DB::statement("
            CREATE POLICY assignments_admin_all ON complaint_assignments
            FOR ALL USING (current_app_user_role() = 'admin');

            CREATE POLICY assignments_management_read ON complaint_assignments
            FOR SELECT USING (
                management_id = current_app_user_id() OR
                current_app_user_role() = 'admin'
            );
        ");

        // Comments RLS Policies
        DB::statement("
            CREATE POLICY comments_read ON complaint_comments
            FOR SELECT USING (
                current_app_user_role() = 'admin' OR
                (
                    visibility = 'public' AND
                    EXISTS (SELECT 1 FROM complaints WHERE complaints.id = complaint_comments.complaint_id AND complaints.student_id = current_app_user_id())
                ) OR
                EXISTS (
                    SELECT 1 FROM complaint_assignments
                    WHERE complaint_assignments.complaint_id = complaint_comments.complaint_id
                    AND complaint_assignments.management_id = current_app_user_id()
                    AND complaint_assignments.is_active = true
                )
            );
        ");

        // Attachments RLS Policies
        DB::statement("
            CREATE POLICY attachments_read ON complaint_attachments
            FOR SELECT USING (
                current_app_user_role() = 'admin' OR
                EXISTS (SELECT 1 FROM complaints WHERE complaints.id = complaint_attachments.complaint_id AND complaints.student_id = current_app_user_id()) OR
                EXISTS (
                    SELECT 1 FROM complaint_assignments
                    WHERE complaint_assignments.complaint_id = complaint_attachments.complaint_id
                    AND complaint_assignments.management_id = current_app_user_id()
                    AND complaint_assignments.is_active = true
                )
            );
        ");

        // Status History RLS
        DB::statement("
            CREATE POLICY status_history_read ON complaint_status_history
            FOR SELECT USING (
                current_app_user_role() = 'admin' OR
                EXISTS (SELECT 1 FROM complaints WHERE complaints.id = complaint_status_history.complaint_id AND complaints.student_id = current_app_user_id()) OR
                EXISTS (
                    SELECT 1 FROM complaint_assignments
                    WHERE complaint_assignments.complaint_id = complaint_status_history.complaint_id
                    AND complaint_assignments.management_id = current_app_user_id()
                    AND complaint_assignments.is_active = true
                )
            );
        ");

        // Verifications RLS
        DB::statement("
            CREATE POLICY verifications_read ON complaint_verifications
            FOR SELECT USING (
                current_app_user_role() = 'admin' OR
                management_id = current_app_user_id() OR
                EXISTS (SELECT 1 FROM complaints WHERE complaints.id = complaint_verifications.complaint_id AND complaints.student_id = current_app_user_id())
            );

            CREATE POLICY verifications_admin_insert ON complaint_verifications
            FOR INSERT WITH CHECK (current_app_user_role() = 'admin');
        ");

        // Notifications RLS
        DB::statement('
            CREATE POLICY notifications_user ON notifications
            FOR ALL USING (user_id = current_app_user_id());
        ');

        // Audit Logs RLS
        DB::statement("
            CREATE POLICY audit_logs_admin_read ON audit_logs
            FOR SELECT USING (current_app_user_role() = 'admin');
        ");

        // Settings RLS
        DB::statement("
            CREATE POLICY settings_read_all ON system_settings
            FOR SELECT USING (current_app_user_id() IS NOT NULL);

            CREATE POLICY settings_admin_modify ON system_settings
            FOR ALL USING (current_app_user_role() = 'admin');
        ");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        $tables = [
            'profiles', 'departments', 'complaint_categories', 'complaints',
            'complaint_assignments', 'complaint_comments', 'complaint_attachments',
            'complaint_status_history', 'complaint_verifications', 'notifications',
            'audit_logs', 'system_settings',
        ];

        foreach ($tables as $table) {
            DB::statement("ALTER TABLE {$table} DISABLE ROW LEVEL SECURITY;");
        }
    }
};
