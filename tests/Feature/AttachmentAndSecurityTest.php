<?php

namespace Tests\Feature;

use App\Models\Complaint;
use App\Models\ComplaintAssignment;
use App\Models\ComplaintAttachment;
use App\Models\ComplaintCategory;
use App\Models\Department;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AttachmentAndSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_file_attachment_upload(): void
    {
        Storage::fake('local');

        $student = Profile::factory()->student()->create();
        $dept = Department::factory()->create();
        $cat = ComplaintCategory::factory()->create(['department_id' => $dept->id]);

        $file = UploadedFile::fake()->create('evidence.pdf', 1024, 'application/pdf');

        $response = $this->actingAs($student)->post(route('student.complaints.store'), [
            'subject' => 'Classroom lighting issue',
            'department_id' => $dept->id,
            'category_id' => $cat->id,
            'description' => 'Light fixture broken in hall B.',
            'attachments' => [$file],
        ]);

        $response->assertRedirect();

        $complaint = Complaint::where('student_id', $student->id)->first();
        $this->assertCount(1, $complaint->attachments);

        $attachment = $complaint->attachments->first();
        $this->assertEquals('evidence.pdf', $attachment->original_filename);
        $this->assertEquals('application/pdf', $attachment->mime_type);
        Storage::disk('local')->assertExists($attachment->storage_path);
    }

    public function test_file_upload_rejects_files_over_5mb(): void
    {
        Storage::fake('local');

        $student = Profile::factory()->student()->create();
        $dept = Department::factory()->create();
        $cat = ComplaintCategory::factory()->create();

        // 6 MB file
        $oversizedFile = UploadedFile::fake()->create('huge.pdf', 6144, 'application/pdf');

        $response = $this->from(route('student.complaints.create'))->actingAs($student)->post(route('student.complaints.store'), [
            'subject' => 'Classroom lighting issue',
            'department_id' => $dept->id,
            'category_id' => $cat->id,
            'description' => 'Light fixture broken in hall B.',
            'attachments' => [$oversizedFile],
        ]);

        $response->assertSessionHasErrors('attachments.0');
    }

    public function test_file_upload_rejects_unauthorized_mimes(): void
    {
        Storage::fake('local');

        $student = Profile::factory()->student()->create();
        $dept = Department::factory()->create();
        $cat = ComplaintCategory::factory()->create();

        $invalidFile = UploadedFile::fake()->create('malicious.sh', 50, 'application/x-sh');

        $response = $this->from(route('student.complaints.create'))->actingAs($student)->post(route('student.complaints.store'), [
            'subject' => 'Classroom lighting issue',
            'department_id' => $dept->id,
            'category_id' => $cat->id,
            'description' => 'Light fixture broken in hall B.',
            'attachments' => [$invalidFile],
        ]);

        $response->assertSessionHasErrors('attachments.0');
    }

    public function test_unauthorized_user_cannot_download_attachment(): void
    {
        Storage::fake('local');

        $owner = Profile::factory()->student()->create();
        $intruder = Profile::factory()->student()->create();
        $complaint = Complaint::factory()->create(['student_id' => $owner->id]);

        Storage::disk('local')->put('test.pdf', 'Dummy PDF Content');

        $attachment = ComplaintAttachment::create([
            'complaint_id' => $complaint->id,
            'uploaded_by' => $owner->id,
            'storage_path' => 'test.pdf',
            'original_filename' => 'test.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
        ]);

        $response = $this->actingAs($intruder)->get(route('student.attachments.download', $attachment->id));

        $response->assertForbidden();
    }

    public function test_authorized_users_can_download_attachment(): void
    {
        Storage::fake('local');

        $owner = Profile::factory()->student()->create();
        $management = Profile::factory()->management()->create();
        $admin = Profile::factory()->admin()->create();

        $complaint = Complaint::factory()->create(['student_id' => $owner->id]);
        ComplaintAssignment::factory()->create([
            'complaint_id' => $complaint->id,
            'management_id' => $management->id,
            'is_active' => true,
        ]);

        Storage::disk('local')->put('test_secure.pdf', 'Secure Content');

        $attachment = ComplaintAttachment::create([
            'complaint_id' => $complaint->id,
            'uploaded_by' => $owner->id,
            'storage_path' => 'test_secure.pdf',
            'original_filename' => 'test_secure.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
        ]);

        // Student owner can download
        $response1 = $this->actingAs($owner)->get(route('student.attachments.download', $attachment->id));
        $response1->assertOk();

        // Assigned management can download
        $response2 = $this->actingAs($management)->get(route('management.attachments.download', $attachment->id));
        $response2->assertOk();

        // Admin can download
        $response3 = $this->actingAs($admin)->get(route('admin.attachments.download', $attachment->id));
        $response3->assertOk();
    }
}
