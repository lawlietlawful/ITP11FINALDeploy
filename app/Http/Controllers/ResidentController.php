<?php

namespace App\Http\Controllers;

use App\Models\Resident;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ResidentController extends Controller {

    public function index(Request $request) {
        $residents = Resident::withCount('documentRequests')
            ->with(['documentRequests.documentType'])
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    $sub->where('first_name', 'like', "%{$request->search}%")
                        ->orWhere('last_name', 'like', "%{$request->search}%")
                        ->orWhere('middle_name', 'like', "%{$request->search}%")
                        ->orWhere('address', 'like', "%{$request->search}%")
                        ->orWhere('resident_id', 'like', "%{$request->search}%");
                });
            })
            ->when($request->gender, fn($q) => $q->where('gender', $request->gender))
            ->when($request->civil_status, fn($q) => $q->where('civil_status', $request->civil_status))
            ->when($request->status, function ($q) use ($request) {
                if ($request->status === 'Active') {
                    $q->whereHas('documentRequests', fn($sub) =>
                        $sub->whereIn('status', ['processing', 'released'])
                            ->where('created_at', '>=', now()->subMonths(6))
                    );
                } elseif ($request->status === 'New') {
                    $q->where('created_at', '>=', now()->subDay())
                      ->whereDoesntHave('documentRequests', fn($sub) =>
                          $sub->whereIn('status', ['processing', 'released'])
                              ->where('created_at', '>=', now()->subMonths(6))
                      );
                } elseif ($request->status === 'Inactive') {
                    $q->where('created_at', '<', now()->subDay())
                      ->whereDoesntHave('documentRequests', fn($sub) =>
                          $sub->whereIn('status', ['processing', 'released'])
                              ->where('created_at', '>=', now()->subMonths(6))
                      );
                }
            })
            ->when($request->sort_by, function ($q) use ($request) {
                $allowed = ['first_name', 'address', 'contact_number', 'birthdate', 'created_at', 'document_requests_count'];
                $col = in_array($request->sort_by, $allowed) ? $request->sort_by : 'created_at';
                $dir = $request->sort_dir === 'asc' ? 'asc' : 'desc';
                $q->orderBy($col, $dir);
            }, function ($q) {
                $q->latest(); // Default sort: newest first
            })
            ->paginate(5)->withQueryString();

        // Analytics stats
        $stats = [
            'total'       => Resident::count(),
            'new_month'   => Resident::where('created_at', '>=', now()->startOfMonth())->count(),
            'active'      => Resident::whereHas('documentRequests', fn($q) =>
                                 $q->whereIn('status', ['processing', 'released'])
                                   ->where('created_at', '>=', now()->subMonths(6))
                             )->count(),
            'total_requests' => \App\Models\DocumentRequest::count(),
        ];

        return view('residents.index', compact('residents', 'stats'));
    }

    public function create() {
        return view('residents.create');
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'first_name'     => 'required|string|max:100',
            'middle_name'    => 'nullable|string|max:100',
            'last_name'      => 'required|string|max:100',
            'address'        => 'required|string|in:Purok 1,Purok 2,Purok 3,Purok 4,Purok 5,Purok 6',
            'contact_number' => ['nullable', 'string', 'regex:/^09\d{9}$/'],
            'birthdate'      => 'nullable|date|before_or_equal:today',
            'gender'         => 'nullable|in:Male,Female',
            'civil_status'   => 'nullable|in:Single,Married,Widowed,Separated',
        ], [
            'contact_number.regex' => 'Contact number must be a valid PH mobile number (e.g., 09171234567).',
            'birthdate.before_or_equal' => 'Birthdate cannot be in the future.',
        ]);

        // Duplicate detection: same first_name + last_name + address
        $duplicate = Resident::where('first_name', $validated['first_name'])
            ->where('last_name', $validated['last_name'])
            ->where('address', $validated['address'])
            ->first();

        if ($duplicate) {
            return redirect()->back()->withInput()->with('warning',
                "A resident named \"{$duplicate->full_name}\" at \"{$duplicate->address}\" already exists. Please verify before creating a duplicate."
            );
        }

        $resident = Resident::create($validated);

        // Audit trail
        ActivityLog::record(
            action: 'created',
            subject: $resident,
            description: "Created resident: {$resident->full_name}",
        );

        return redirect()->route('residents.index')->with('success', 'Resident added successfully.');
    }

    public function show(Resident $resident) {
        $requests = $resident->documentRequests()->with('documentType')->latest()->get();
        return view('residents.show', compact('resident', 'requests'));
    }

    public function edit(Resident $resident) {
        return view('residents.edit', compact('resident'));
    }

    public function update(Request $request, Resident $resident) {
        $validated = $request->validate([
            'first_name'     => 'required|string|max:100',
            'middle_name'    => 'nullable|string|max:100',
            'last_name'      => 'required|string|max:100',
            'address'        => 'required|string|in:Purok 1,Purok 2,Purok 3,Purok 4,Purok 5,Purok 6',
            'contact_number' => ['nullable', 'string', 'regex:/^09\d{9}$/'],
            'birthdate'      => 'nullable|date|before_or_equal:today',
            'gender'         => 'nullable|in:Male,Female',
            'civil_status'   => 'nullable|in:Single,Married,Widowed,Separated',
        ], [
            'contact_number.regex' => 'Contact number must be a valid PH mobile number (e.g., 09171234567).',
            'birthdate.before_or_equal' => 'Birthdate cannot be in the future.',
        ]);

        $oldName = $resident->full_name;
        $resident->update($validated);

        // Audit trail
        ActivityLog::record(
            action: 'updated',
            subject: $resident,
            description: "Updated resident: {$oldName}",
        );

        return redirect()->route('residents.index')->with('success', 'Resident updated successfully.');
    }

    public function destroy(Resident $resident) {
        // Safe delete guard: block if resident has active (pending/processing) requests
        $activeRequests = $resident->documentRequests()
            ->whereIn('status', ['pending', 'processing'])
            ->count();

        if ($activeRequests > 0) {
            return redirect()->route('residents.index')->with('error',
                "Cannot delete {$resident->full_name} — they have {$activeRequests} active request(s) still being processed. Please resolve them first."
            );
        }

        $name = $resident->full_name;
        $resident->delete();

        // Audit trail
        ActivityLog::record(
            action: 'deleted',
            subject: $resident,
            description: "Deleted resident: {$name}",
        );

        return redirect()->route('residents.index')->with('success', 'Resident deleted.');
    }

    /**
     * Export all residents as CSV.
     */
    public function export(): StreamedResponse {
        $residents = Resident::withCount('documentRequests')->latest()->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="residents_export_' . date('Y-m-d') . '.csv"',
        ];

        return response()->stream(function () use ($residents) {
            $handle = fopen('php://output', 'w');

            // Header row
            fputcsv($handle, [
                'Resident ID', 'First Name', 'Middle Name', 'Last Name', 'Full Name',
                'Address', 'Contact Number', 'Gender', 'Civil Status',
                'Birthdate', 'Age', 'Status', 'Total Requests', 'Date Registered',
            ]);

            // Data rows
            foreach ($residents as $r) {
                fputcsv($handle, [
                    $r->resident_id ?? '',
                    $r->first_name,
                    $r->middle_name ?? '',
                    $r->last_name,
                    $r->full_name,
                    $r->address,
                    $r->contact_number ?? '',
                    $r->gender ?? '',
                    $r->civil_status ?? '',
                    $r->birthdate?->format('Y-m-d') ?? '',
                    $r->age ?? '',
                    $r->status,
                    $r->document_requests_count,
                    $r->created_at->format('Y-m-d'),
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Import residents from a CSV file.
     * Expected columns: first_name, middle_name, last_name, address, contact_number, birthdate, gender, civil_status
     */
    public function import(Request $request) {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');

        // Read header row
        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            return back()->with('error', 'CSV file is empty or malformed.');
        }

        // Normalize headers (lowercase, trim, replace spaces with underscores)
        $header = array_map(fn($h) => strtolower(trim(str_replace(' ', '_', $h))), $header);

        // Required columns check
        $requiredCols = ['first_name', 'last_name', 'address'];
        $missingCols = array_diff($requiredCols, $header);
        if (count($missingCols) > 0) {
            fclose($handle);
            return back()->with('error', 'CSV is missing required columns: ' . implode(', ', $missingCols));
        }

        $imported = 0;
        $skipped  = 0;
        $errors   = [];
        $rowNum   = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNum++;

            // Skip empty rows
            if (count(array_filter($row)) === 0) continue;

            // Map columns to values
            $data = [];
            foreach ($header as $i => $col) {
                $data[$col] = $row[$i] ?? null;
            }

            // Validate required fields
            if (empty($data['first_name']) || empty($data['last_name']) || empty($data['address'])) {
                $skipped++;
                $errors[] = "Row {$rowNum}: Missing required field(s).";
                continue;
            }

            // Duplicate detection
            $exists = Resident::where('first_name', $data['first_name'])
                ->where('last_name', $data['last_name'])
                ->where('address', $data['address'])
                ->exists();

            if ($exists) {
                $skipped++;
                $errors[] = "Row {$rowNum}: Duplicate — {$data['first_name']} {$data['last_name']} at {$data['address']}.";
                continue;
            }

            // Validate contact number if provided
            $contact = $data['contact_number'] ?? null;
            if ($contact && !preg_match('/^09\d{9}$/', $contact)) {
                $contact = null; // Silently drop invalid numbers
            }

            // Validate gender
            $gender = $data['gender'] ?? null;
            if ($gender && !in_array($gender, ['Male', 'Female'])) {
                $gender = null;
            }

            // Validate civil status
            $civilStatus = $data['civil_status'] ?? null;
            if ($civilStatus && !in_array($civilStatus, ['Single', 'Married', 'Widowed', 'Separated'])) {
                $civilStatus = null;
            }

            // Validate birthdate
            $birthdate = null;
            if (!empty($data['birthdate'])) {
                try {
                    $birthdate = \Carbon\Carbon::parse($data['birthdate'])->format('Y-m-d');
                } catch (\Exception $e) {
                    $birthdate = null;
                }
            }

            // Create resident (resident_id auto-generated by model boot)
            Resident::create([
                'first_name'     => trim($data['first_name']),
                'middle_name'    => trim($data['middle_name'] ?? ''),
                'last_name'      => trim($data['last_name']),
                'address'        => trim($data['address']),
                'contact_number' => $contact,
                'birthdate'      => $birthdate,
                'gender'         => $gender,
                'civil_status'   => $civilStatus,
            ]);

            // Audit log
            ActivityLog::create([
                'user_id'     => auth()->id(),
                'action'      => 'imported',
                'description' => "Imported resident: {$data['first_name']} {$data['last_name']} (CSV import)",
            ]);

            $imported++;
        }

        fclose($handle);

        $message = "✅ Imported {$imported} resident(s).";
        if ($skipped > 0) {
            $message .= " Skipped {$skipped} row(s): " . implode(' | ', array_slice($errors, 0, 5));
            if (count($errors) > 5) {
                $message .= ' ...and ' . (count($errors) - 5) . ' more.';
            }
        }

        return back()->with('success', $message);
    }
}
