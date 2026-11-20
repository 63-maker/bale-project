<?php

namespace App\Services;

use App\Models\Complaint;
use App\Models\ComplaintAttachment;
use App\Models\Profile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AttachmentService
{
    public const MAX_FILE_SIZE_KB = 5120; // 5 MB

    public const MAX_FILES_PER_COMPLAINT = 5;

    public const ALLOWED_MIMES = [
        'image/jpeg',
        'image/png',
        'image/webp',
        'application/pdf',
    ];

    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * Validate and store an attachment for a complaint
     */
    public function storeAttachment(
        UploadedFile $file,
        Complaint $complaint,
        Profile $uploader
    ): ComplaintAttachment {
        $existingCount = $complaint->attachments()->count();
        if ($existingCount >= self::MAX_FILES_PER_COMPLAINT) {
            throw ValidationException::withMessages([
                'attachments' => 'Maximum limit of '.self::MAX_FILES_PER_COMPLAINT.' attachments per complaint reached.',
            ]);
        }

        $mimeType = $file->getMimeType();
        if (! in_array($mimeType, self::ALLOWED_MIMES, true)) {
            throw ValidationException::withMessages([
                'attachments' => 'Invalid file format. Allowed file types: JPG, PNG, WebP, PDF.',
            ]);
        }

        if ($file->getSize() > (self::MAX_FILE_SIZE_KB * 1024)) {
            throw ValidationException::withMessages([
                'attachments' => 'File size exceeds maximum permitted limit of 5 MB.',
            ]);
        }

        $extension = $file->getClientOriginalExtension() ?: 'bin';
        $randomName = Str::random(40).'.'.strtolower($extension);
        $relativeFolder = 'complaint-attachments/'.$complaint->id;

        $path = $file->storeAs($relativeFolder, $randomName, 'local');

        $attachment = ComplaintAttachment::create([
            'complaint_id' => $complaint->id,
            'uploaded_by' => $uploader->id,
            'storage_path' => $path,
            'original_filename' => $file->getClientOriginalName(),
            'mime_type' => $mimeType,
            'file_size' => $file->getSize(),
        ]);

        $this->auditService->log(
            action: 'ATTACHMENT_UPLOADED',
            entityType: 'ComplaintAttachment',
            entityId: $attachment->id,
            details: [
                'complaint_id' => $complaint->id,
                'filename' => $attachment->original_filename,
                'size' => $attachment->file_size,
            ],
            actor: $uploader
        );

        return $attachment;
    }

    /**
     * Download attachment verifying permission
     */
    public function downloadAttachment(ComplaintAttachment $attachment, Profile $user): BinaryFileResponse
    {
        $complaint = $attachment->complaint;

        // Verify authorization
        $canAccess = false;

        if ($user->isAdmin()) {
            $canAccess = true;
        } elseif ($user->isStudent() && $complaint->student_id === $user->id) {
            $canAccess = true;
        } elseif ($user->isManagement() && $complaint->isAssignedTo($user->id)) {
            $canAccess = true;
        }

        if (! $canAccess) {
            abort(403, 'Unauthorized to access this attachment.');
        }

        if (! Storage::disk('local')->exists($attachment->storage_path)) {
            abort(404, 'Attachment file not found on storage.');
        }

        $fullPath = Storage::disk('local')->path($attachment->storage_path);

        $this->auditService->log(
            action: 'ATTACHMENT_DOWNLOADED',
            entityType: 'ComplaintAttachment',
            entityId: $attachment->id,
            details: ['original_filename' => $attachment->original_filename],
            actor: $user
        );

        return response()->download($fullPath, $attachment->original_filename, [
            'Content-Type' => $attachment->mime_type,
            'Cache-Control' => 'no-cache, private',
        ]);
    }
}
