<?php

namespace Tests\Feature;

use App\Models\DocumentRequest;
use App\Models\DocumentType;
use App\Models\Resident;
use App\Models\User;
use App\Notifications\NewDocumentRequestNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_document_requests_create_database_notifications_for_admins(): void
    {
        $admin = User::factory()->create();
        $resident = $this->createResident();
        $documentType = $this->createDocumentType();

        $documentRequest = DocumentRequest::create([
            'tracking_code' => DocumentRequest::generateTrackingCode(),
            'resident_id' => $resident->id,
            'document_type_id' => $documentType->id,
            'purpose' => 'Testing notification persistence',
            'status' => 'pending',
        ]);

        $admin->refresh();

        $this->assertCount(1, $admin->notifications);
        $this->assertSame(NewDocumentRequestNotification::class, $admin->notifications->first()->type);
        $this->assertSame($documentRequest->id, $admin->notifications->first()->data['request_id']);
        $this->assertSame(
            route('requests.index', ['open_request' => $documentRequest->id]),
            $admin->notifications->first()->data['request_url']
        );
    }

    public function test_admins_can_mark_all_notifications_as_read(): void
    {
        $admin = User::factory()->create();
        $resident = $this->createResident();
        $documentType = $this->createDocumentType();

        DocumentRequest::create([
            'tracking_code' => DocumentRequest::generateTrackingCode(),
            'resident_id' => $resident->id,
            'document_type_id' => $documentType->id,
            'purpose' => 'Testing read state',
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->postJson(route('notifications.markAllRead'))
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertSame(0, $admin->fresh()->unreadNotifications()->count());
    }

    public function test_dashboard_renders_admin_notifications(): void
    {
        $admin = User::factory()->create();
        $resident = $this->createResident();
        $documentType = $this->createDocumentType();

        DocumentRequest::create([
            'tracking_code' => DocumentRequest::generateTrackingCode(),
            'resident_id' => $resident->id,
            'document_type_id' => $documentType->id,
            'purpose' => 'Testing dashboard render',
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee($resident->full_name)
            ->assertSee($documentType->name);
    }

    private function createResident(): Resident
    {
        return Resident::create([
            'first_name' => 'Juan',
            'middle_name' => 'Dela',
            'last_name' => 'Cruz',
            'address' => 'Purok 1',
            'contact_number' => '09171234567',
            'birthdate' => '1990-01-01',
            'gender' => 'Male',
            'civil_status' => 'Single',
        ]);
    }

    private function createDocumentType(): DocumentType
    {
        return DocumentType::create([
            'name' => 'Barangay Clearance',
            'category' => 'Clearance',
            'description' => 'General clearance',
            'requirements' => 'Valid ID',
            'fee' => 100,
            'processing_days' => 1,
            'is_active' => true,
            'sort_order' => 1,
        ]);
    }
}
