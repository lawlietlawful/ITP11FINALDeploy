<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\DocumentRequest;
use App\Models\DocumentType;
use App\Models\Resident;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RealtimeResilienceTest extends TestCase
{
    use RefreshDatabase;

    public function test_document_request_creation_does_not_fail_if_notification_sending_breaks(): void
    {
        $admin = User::factory()->create();
        $resident = $this->createResident();
        $documentType = $this->createDocumentType();

        $this->configureBrokenReverbConnection();

        $request = DocumentRequest::create([
            'tracking_code' => DocumentRequest::generateTrackingCode(),
            'resident_id' => $resident->id,
            'document_type_id' => $documentType->id,
            'purpose' => 'Realtime resilience test',
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('document_requests', ['id' => $request->id]);
    }

    public function test_activity_log_record_does_not_fail_if_broadcast_dispatch_breaks(): void
    {
        $admin = User::factory()->create();
        $resident = $this->createResident();

        $this->configureBrokenReverbConnection();

        $this->actingAs($admin);

        $log = ActivityLog::record(
            action: 'deleted',
            subject: $resident,
            description: 'Deleted resident during resilience test',
        );

        $this->assertDatabaseHas('activity_logs', ['id' => $log->id]);
    }

    private function configureBrokenReverbConnection(): void
    {
        config([
            'broadcasting.default' => 'reverb',
            'broadcasting.connections.reverb.key' => 'test-key',
            'broadcasting.connections.reverb.secret' => 'test-secret',
            'broadcasting.connections.reverb.app_id' => 'test-app',
            'broadcasting.connections.reverb.options' => [
                'host' => '127.0.0.1',
                'port' => 65530,
                'scheme' => 'http',
                'useTLS' => false,
            ],
        ]);
    }

    private function createResident(): Resident
    {
        return Resident::create([
            'first_name' => 'Maria',
            'middle_name' => 'Santos',
            'last_name' => 'Reyes',
            'address' => 'Purok 1',
            'contact_number' => '09171234567',
            'birthdate' => '1990-01-01',
            'gender' => 'Female',
            'civil_status' => 'Single',
        ]);
    }

    private function createDocumentType(): DocumentType
    {
        return DocumentType::create([
            'name' => 'Residency Certification',
            'category' => 'Certificate',
            'description' => 'Certification of residency',
            'requirements' => 'Valid ID',
            'fee' => 50,
            'processing_days' => 1,
            'is_active' => true,
            'sort_order' => 1,
        ]);
    }
}
