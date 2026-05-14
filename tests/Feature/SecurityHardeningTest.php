<?php

namespace Tests\Feature;

use App\Models\DocumentRequest;
use App\Models\DocumentType;
use App\Models\Resident;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_submission_does_not_overwrite_existing_resident_profile(): void
    {
        $existingResident = Resident::create([
            'first_name' => 'Juan',
            'middle_name' => 'Dela',
            'last_name' => 'Cruz',
            'address' => 'Purok 1',
            'contact_number' => '09171234567',
            'birthdate' => '1990-01-01',
            'gender' => 'Male',
            'civil_status' => 'Single',
        ]);

        $documentType = $this->createDocumentType();

        $response = $this->post(route('public.submit'), [
            'first_name' => 'Juan',
            'middle_name' => 'Dela',
            'last_name' => 'Cruz',
            'address' => 'Purok 2',
            'contact_number' => '09999999999',
            'birthdate' => '1990-01-01',
            'gender' => 'Male',
            'civil_status' => 'Married',
            'document_type_id' => $documentType->id,
            'purpose' => 'Security regression test',
        ]);

        $documentRequest = DocumentRequest::latest('id')->first();

        $response->assertRedirect(route('public.success', $documentRequest->tracking_code));

        $this->assertSame(2, Resident::count());
        $this->assertSame('Purok 1', $existingResident->fresh()->address);
        $this->assertSame('09171234567', $existingResident->fresh()->contact_number);
        $this->assertSame('Single', $existingResident->fresh()->civil_status);
    }

    public function test_public_request_honeypot_is_blocked_and_logged(): void
    {
        $response = $this->post(route('public.submit'), [
            'website' => 'https://bot.example',
            'first_name' => 'Bot',
            'last_name' => 'User',
            'address' => 'Purok 1',
        ]);

        $response->assertRedirect(route('public.home'));
        $this->assertSame(0, DocumentRequest::count());
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'blocked',
            'description' => 'Blocked public request because the honeypot field was filled.',
        ]);
    }

    public function test_public_tracking_rejects_invalid_tracking_code_format(): void
    {
        $this->from(route('public.track'))
            ->post(route('public.track.search'), [
                'tracking_code' => 'not-a-valid-code',
            ])
            ->assertRedirect(route('public.track'))
            ->assertSessionHasErrors('tracking_code');
    }

    public function test_public_tracking_page_shows_only_limited_request_details(): void
    {
        $resident = Resident::create([
            'first_name' => 'Ana',
            'middle_name' => 'Santos',
            'last_name' => 'Reyes',
            'address' => 'Purok 3',
            'contact_number' => '09170000000',
            'birthdate' => '1995-05-05',
            'gender' => 'Female',
            'civil_status' => 'Single',
        ]);

        $documentType = $this->createDocumentType();

        $request = DocumentRequest::create([
            'tracking_code' => 'BDRS-ABCDEF1234',
            'resident_id' => $resident->id,
            'document_type_id' => $documentType->id,
            'purpose' => 'Sensitive purpose text',
            'status' => 'rejected',
            'rejection_reason' => 'Sensitive internal note',
        ]);

        $this->post(route('public.track.search'), [
            'tracking_code' => $request->tracking_code,
        ])
            ->assertOk()
            ->assertSee($request->tracking_code)
            ->assertSee($documentType->name)
            ->assertDontSee($resident->full_name)
            ->assertDontSee($request->purpose)
            ->assertDontSee($request->rejection_reason);
    }

    public function test_repeated_public_request_attempts_trigger_identity_cooldown(): void
    {
        $documentType = $this->createDocumentType();
        $resident = Resident::create([
            'first_name' => 'Nina',
            'middle_name' => 'Mae',
            'last_name' => 'Torres',
            'address' => 'Purok 2',
            'contact_number' => '09174444444',
            'birthdate' => '1994-04-04',
            'gender' => 'Female',
            'civil_status' => 'Single',
        ]);

        DocumentRequest::create([
            'tracking_code' => DocumentRequest::generateTrackingCode(),
            'resident_id' => $resident->id,
            'document_type_id' => $documentType->id,
            'purpose' => 'Existing request',
            'status' => 'pending',
        ]);

        $payload = [
            'first_name' => 'Nina',
            'middle_name' => 'Mae',
            'last_name' => 'Torres',
            'address' => 'Purok 2',
            'contact_number' => '09174444444',
            'birthdate' => '1994-04-04',
            'gender' => 'Female',
            'civil_status' => 'Single',
            'document_type_id' => $documentType->id,
            'purpose' => 'Identity cooldown test',
        ];

        foreach (range(1, 3) as $attempt) {
            $this->from(route('public.request'))
                ->post(route('public.submit'), $payload)
                ->assertRedirect(route('public.request'))
                ->assertSessionHasErrors('document_type_id');
        }

        $this->from(route('public.request'))
            ->post(route('public.submit'), $payload)
            ->assertRedirect(route('public.request'))
            ->assertSessionHasErrors('throttle');

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'throttled',
            'description' => 'Blocked public request because the identity cooldown is active for another 300 second(s).',
        ]);
    }

    public function test_public_tracking_applies_cooldown_after_repeated_failed_lookups(): void
    {
        foreach (range(1, 5) as $attempt) {
            $response = $this->post(route('public.track.search'), [
                'tracking_code' => 'BDRS-ABCDEF1234',
            ]);

            $response->assertOk();
        }

        $this->from(route('public.track'))
            ->post(route('public.track.search'), [
                'tracking_code' => 'BDRS-1234567890',
            ])
            ->assertRedirect(route('public.track'))
            ->assertSessionHasErrors('tracking_code');
    }

    public function test_successful_public_tracking_clears_failed_lookup_cooldown_state(): void
    {
        $resident = Resident::create([
            'first_name' => 'Lia',
            'middle_name' => null,
            'last_name' => 'Garcia',
            'address' => 'Purok 2',
            'contact_number' => '09173333333',
            'birthdate' => '1996-06-06',
            'gender' => 'Female',
            'civil_status' => 'Single',
        ]);

        $documentType = $this->createDocumentType();

        $request = DocumentRequest::create([
            'tracking_code' => 'BDRS-112233AABB',
            'resident_id' => $resident->id,
            'document_type_id' => $documentType->id,
            'purpose' => 'Cooldown reset test',
            'status' => 'pending',
        ]);

        foreach (range(1, 4) as $attempt) {
            $this->post(route('public.track.search'), [
                'tracking_code' => 'BDRS-FFEEDDCCBB',
            ])->assertOk();
        }

        $this->post(route('public.track.search'), [
            'tracking_code' => $request->tracking_code,
        ])->assertOk();

        $failedKey = 'public-track-failed:' . sha1('127.0.0.1');
        $cooldownKey = 'public-track-cooldown:' . sha1('127.0.0.1');

        $this->assertSame(0, RateLimiter::attempts($failedKey));
        $this->assertSame(0, RateLimiter::attempts($cooldownKey));
    }

    public function test_resident_export_escapes_spreadsheet_formulas(): void
    {
        $admin = User::factory()->create();

        Resident::create([
            'first_name' => '=cmd',
            'middle_name' => null,
            'last_name' => 'User',
            'address' => 'Purok 4',
            'contact_number' => '09171111111',
            'birthdate' => '1992-02-02',
            'gender' => 'Male',
            'civil_status' => 'Single',
        ]);

        $response = $this->actingAs($admin)->get(route('residents.export'));

        $response->assertOk();
        $this->assertStringContainsString("'=cmd", $response->streamedContent());
    }

    public function test_reports_export_escapes_spreadsheet_formulas(): void
    {
        $admin = User::factory()->create();
        $resident = Resident::create([
            'first_name' => 'Mia',
            'middle_name' => null,
            'last_name' => 'Lopez',
            'address' => 'Purok 5',
            'contact_number' => '09172222222',
            'birthdate' => '1993-03-03',
            'gender' => 'Female',
            'civil_status' => 'Single',
        ]);

        $documentType = DocumentType::create([
            'name' => 'Barangay Clearance',
            'category' => 'Clearance',
            'description' => 'General clearance',
            'requirements' => 'Valid ID',
            'fee' => 100,
            'processing_days' => 1,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        DocumentRequest::create([
            'tracking_code' => DocumentRequest::generateTrackingCode(),
            'resident_id' => $resident->id,
            'document_type_id' => $documentType->id,
            'purpose' => '=HYPERLINK(\"http://malicious.test\")',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->get(route('reports.export'));

        $response->assertOk();
        $this->assertStringContainsString("'=HYPERLINK", $response->streamedContent());
    }

    public function test_resident_import_rejects_invalid_rows_instead_of_silently_normalizing_them(): void
    {
        $admin = User::factory()->create();

        $csv = implode("\n", [
            'first_name,last_name,address,contact_number,birthdate,gender,civil_status',
            'Bad,Row,Unknown Purok,123,not-a-date,Robot,Complicated',
        ]);

        $file = UploadedFile::fake()->createWithContent('residents.csv', $csv);

        $response = $this->actingAs($admin)
            ->from(route('residents.index'))
            ->post(route('residents.import'), [
                'csv_file' => $file,
            ]);

        $response->assertRedirect(route('residents.index'));
        $response->assertSessionHas('success');

        $this->assertSame(0, Resident::count());
        $this->assertStringContainsString('Skipped 1 row(s): Row 2:', session('success'));
    }

    private function createDocumentType(): DocumentType
    {
        return DocumentType::create([
            'name' => 'Barangay Certificate',
            'category' => 'Certificate',
            'description' => 'General certificate',
            'requirements' => 'Valid ID',
            'fee' => 75,
            'processing_days' => 1,
            'is_active' => true,
            'sort_order' => 1,
        ]);
    }
}
