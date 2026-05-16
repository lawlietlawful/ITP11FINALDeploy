<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Resident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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
                $q->latest();
            })
            ->paginate(5)->withQueryString();

        $stats = [
            'total' => Resident::count(),
            'new_month' => Resident::where('created_at', '>=', now()->startOfMonth())->count(),
            'active' => Resident::whereHas('documentRequests', fn($q) =>
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
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'address' => 'required|string|in:Purok 1,Purok 2,Purok 3,Purok 4,Purok 5,Purok 6',
            'contact_number' => ['nullable', 'string', 'regex:/^09\d{9}$/'],
            'email' => 'nullable|email|max:255',
            'birthdate' => 'nullable|date|before_or_equal:' . now()->subYears(18)->format('Y-m-d'),
            'gender' => 'nullable|in:Male,Female',
            'civil_status' => 'nullable|in:Single,Married,Widowed,Separated',
        ], [
            'contact_number.regex' => 'Contact number must be a valid PH mobile number (e.g., 09171234567).',
            'birthdate.before_or_equal' => 'Resident must be at least 18 years old.',
        ]);

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
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'address' => 'required|string|in:Purok 1,Purok 2,Purok 3,Purok 4,Purok 5,Purok 6',
            'contact_number' => ['nullable', 'string', 'regex:/^09\d{9}$/'],
            'email' => 'nullable|email|max:255',
            'birthdate' => 'nullable|date|before_or_equal:' . now()->subYears(18)->format('Y-m-d'),
            'gender' => 'nullable|in:Male,Female',
            'civil_status' => 'nullable|in:Single,Married,Widowed,Separated',
        ], [
            'contact_number.regex' => 'Contact number must be a valid PH mobile number (e.g., 09171234567).',
            'birthdate.before_or_equal' => 'Resident must be at least 18 years old.',
        ]);

        $oldName = $resident->full_name;
        $resident->update($validated);

        ActivityLog::record(
            action: 'updated',
            subject: $resident,
            description: "Updated resident: {$oldName}",
        );

        return redirect()->route('residents.index')->with('success', 'Resident updated successfully.');
    }

    public function destroy(Resident $resident) {
        $activeRequests = $resident->documentRequests()
            ->whereIn('status', ['pending', 'processing'])
            ->count();

        if ($activeRequests > 0) {
            return redirect()->route('residents.index')->with('error',
                "Cannot delete {$resident->full_name} - they have {$activeRequests} active request(s) still being processed. Please resolve them first."
            );
        }

        $name = $resident->full_name;
        $resident->delete();

        ActivityLog::record(
            action: 'deleted',
            subject: $resident,
            description: "Deleted resident: {$name}",
        );

        return redirect()->route('residents.index')->with('success', 'Resident deleted.');
    }

    public function export(): StreamedResponse {
        $residents = Resident::withCount('documentRequests')->latest()->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="residents_export_' . date('Y-m-d') . '.csv"',
        ];

        return response()->stream(function () use ($residents) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Resident ID', 'First Name', 'Middle Name', 'Last Name', 'Full Name',
                'Address', 'Contact Number', 'Gender', 'Civil Status',
                'Birthdate', 'Age', 'Status', 'Total Requests', 'Date Registered',
            ]);

            foreach ($residents as $resident) {
                fputcsv($handle, [
                    $this->escapeCsvFormula($resident->resident_id ?? ''),
                    $this->escapeCsvFormula($resident->first_name),
                    $this->escapeCsvFormula($resident->middle_name ?? ''),
                    $this->escapeCsvFormula($resident->last_name),
                    $this->escapeCsvFormula($resident->full_name),
                    $this->escapeCsvFormula($resident->address),
                    $this->escapeCsvFormula($resident->contact_number ?? ''),
                    $this->escapeCsvFormula($resident->gender ?? ''),
                    $this->escapeCsvFormula($resident->civil_status ?? ''),
                    $this->escapeCsvFormula($resident->birthdate?->format('Y-m-d') ?? ''),
                    $this->escapeCsvFormula($resident->age ?? ''),
                    $this->escapeCsvFormula($resident->status),
                    $this->escapeCsvFormula($resident->document_requests_count),
                    $this->escapeCsvFormula($resident->created_at->format('Y-m-d')),
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    public function import(Request $request) {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');

        $header = fgetcsv($handle);
        if (! $header) {
            fclose($handle);

            return back()->with('error', 'CSV file is empty or malformed.');
        }

        $header = array_map(fn($value) => strtolower(trim(str_replace(' ', '_', $value))), $header);

        $requiredCols = ['first_name', 'last_name', 'address'];
        $missingCols = array_diff($requiredCols, $header);
        if (count($missingCols) > 0) {
            fclose($handle);

            return back()->with('error', 'CSV is missing required columns: ' . implode(', ', $missingCols));
        }

        $imported = 0;
        $skipped = 0;
        $errors = [];
        $rowNum = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNum++;

            if (count(array_filter($row)) === 0) {
                continue;
            }

            $data = [];
            foreach ($header as $i => $column) {
                $data[$column] = $row[$i] ?? null;
            }

            $normalizedData = [
                'first_name' => trim((string) ($data['first_name'] ?? '')),
                'middle_name' => $this->nullIfEmpty($data['middle_name'] ?? null),
                'last_name' => trim((string) ($data['last_name'] ?? '')),
                'address' => trim((string) ($data['address'] ?? '')),
                'contact_number' => $this->nullIfEmpty($data['contact_number'] ?? null),
                'birthdate' => $this->nullIfEmpty($data['birthdate'] ?? null),
                'gender' => $this->nullIfEmpty($data['gender'] ?? null),
                'civil_status' => $this->nullIfEmpty($data['civil_status'] ?? null),
            ];

            $validator = Validator::make($normalizedData, [
                'first_name' => 'required|string|max:100',
                'middle_name' => 'nullable|string|max:100',
                'last_name' => 'required|string|max:100',
                'address' => 'required|string|in:Purok 1,Purok 2,Purok 3,Purok 4,Purok 5,Purok 6',
                'contact_number' => ['nullable', 'string', 'regex:/^09\d{9}$/'],
                'birthdate' => 'nullable|date|before_or_equal:' . now()->subYears(18)->format('Y-m-d'),
                'gender' => 'nullable|in:Male,Female',
                'civil_status' => 'nullable|in:Single,Married,Widowed,Separated',
            ]);

            if ($validator->fails()) {
                $skipped++;
                $errors[] = "Row {$rowNum}: " . $validator->errors()->first();
                continue;
            }

            $clean = $validator->validated();

            $exists = Resident::where('first_name', $clean['first_name'])
                ->where('last_name', $clean['last_name'])
                ->where('address', $clean['address'])
                ->exists();

            if ($exists) {
                $skipped++;
                $errors[] = "Row {$rowNum}: Duplicate - {$clean['first_name']} {$clean['last_name']} at {$clean['address']}.";
                continue;
            }

            Resident::create([
                'first_name' => $clean['first_name'],
                'middle_name' => $clean['middle_name'] ?? null,
                'last_name' => $clean['last_name'],
                'address' => $clean['address'],
                'contact_number' => $clean['contact_number'] ?? null,
                'birthdate' => $clean['birthdate'] ?? null,
                'gender' => $clean['gender'] ?? null,
                'civil_status' => $clean['civil_status'] ?? null,
            ]);

            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'imported',
                'description' => "Imported resident: {$clean['first_name']} {$clean['last_name']} (CSV import)",
            ]);

            $imported++;
        }

        fclose($handle);

        $message = "Imported {$imported} resident(s).";
        if ($skipped > 0) {
            $message .= " Skipped {$skipped} row(s): " . implode(' | ', array_slice($errors, 0, 5));
            if (count($errors) > 5) {
                $message .= ' ...and ' . (count($errors) - 5) . ' more.';
            }
        }

        return back()->with('success', $message);
    }

    private function escapeCsvFormula(mixed $value): mixed
    {
        if (! is_string($value)) {
            return $value;
        }

        $trimmed = ltrim($value);

        if ($trimmed !== '' && in_array($trimmed[0], ['=', '+', '-', '@'], true)) {
            return "'" . $value;
        }

        return $value;
    }

    private function nullIfEmpty(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim((string) $value);

        return $trimmed === '' ? null : $trimmed;
    }
}
