<?php

namespace App\Http\Controllers;

use App\Models\DocumentRequest;
use App\Models\DocumentType;
use Illuminate\Http\Request;

class ReportController extends Controller {

    public function index(Request $request) {
        $baseQuery = DocumentRequest::query()
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->document_type_id, fn($q) => $q->where('document_type_id', $request->document_type_id))
            ->when($request->date_from, fn($q) => $q->whereDate('document_requests.created_at', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('document_requests.created_at', '<=', $request->date_to))
            ->when($request->search, fn($q) =>
                $q->whereHas('resident', fn($r) =>
                    $r->where('first_name', 'like', "%{$request->search}%")
                      ->orWhere('last_name', 'like', "%{$request->search}%")
                )
            );

        $stats = [
            'total_requests' => (clone $baseQuery)->count(),
            'total_revenue' => (clone $baseQuery)
                ->where('document_requests.status', '!=', 'pending')
                ->join('document_types', 'document_requests.document_type_id', '=', 'document_types.id')
                ->sum('document_types.fee'),
            'approved' => (clone $baseQuery)->whereIn('status', ['processing', 'released'])->count(),
            'pending' => (clone $baseQuery)->where('status', 'pending')->count(),
        ];

        $requests = (clone $baseQuery)
            ->with(['resident', 'documentType', 'processedBy'])
            ->latest('document_requests.created_at')
            ->paginate(5)
            ->withQueryString();

        $documentTypes = DocumentType::orderBy('name')->get();

        return view('reports.index', compact('requests', 'documentTypes', 'stats'));
    }

    public function export(Request $request) {
        $baseQuery = DocumentRequest::query()
            ->with(['resident', 'documentType', 'processedBy'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->document_type_id, fn($q) => $q->where('document_type_id', $request->document_type_id))
            ->when($request->date_from, fn($q) => $q->whereDate('document_requests.created_at', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('document_requests.created_at', '<=', $request->date_to))
            ->when($request->search, fn($q) =>
                $q->whereHas('resident', fn($r) =>
                    $r->where('first_name', 'like', "%{$request->search}%")
                      ->orWhere('last_name', 'like', "%{$request->search}%")
                )
            )
            ->latest('document_requests.created_at');

        $requests = $baseQuery->get();

        $filename = 'document_requests_report_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($requests) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Resident', 'Document Type', 'Fee', 'Purpose', 'Status', 'Processed By', 'Date Requested']);

            foreach ($requests as $request) {
                fputcsv($file, [
                    $this->escapeCsvFormula($request->id),
                    $this->escapeCsvFormula($request->resident->full_name),
                    $this->escapeCsvFormula($request->documentType->name),
                    $this->escapeCsvFormula((string) $request->documentType->fee),
                    $this->escapeCsvFormula($request->purpose),
                    $this->escapeCsvFormula(ucfirst($request->status)),
                    $this->escapeCsvFormula($request->processedBy->name ?? '-'),
                    $this->escapeCsvFormula($request->created_at->format('Y-m-d H:i:s')),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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
}
