<?php

namespace App\Http\Controllers;

use App\Models\DocumentRequest;
use App\Models\DocumentType;
use Illuminate\Http\Request;

class ReportController extends Controller {

    public function index(Request $request) {
        $query = DocumentRequest::with(['resident', 'documentType', 'processedBy'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->document_type_id, fn($q) => $q->where('document_type_id', $request->document_type_id))
            ->when($request->date_from, fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->when($request->search, fn($q) =>
                $q->whereHas('resident', fn($r) =>
                    $r->where('first_name', 'like', "%{$request->search}%")
                      ->orWhere('last_name', 'like', "%{$request->search}%")
                )
            )
            ->latest();

        $requests      = $query->paginate(15)->withQueryString();
        $documentTypes = DocumentType::orderBy('name')->get();

        return view('reports.index', compact('requests', 'documentTypes'));
    }
}
