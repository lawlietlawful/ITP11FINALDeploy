<?php

namespace App\Http\Controllers;

use App\Models\DocumentType;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocumentTypeController extends Controller {

    public function index(Request $request) {
        $sort = $request->get('sort', 'sort_order');
        $direction = $request->get('direction', 'asc');
        
        $allowedSorts = ['name', 'fee', 'processing_days', 'is_active', 'created_at', 'sort_order'];
        $sort = in_array($sort, $allowedSorts) ? $sort : 'sort_order';
        $direction = in_array(strtolower($direction), ['asc', 'desc']) ? $direction : 'asc';

        $types = DocumentType::orderBy($sort, $direction)
            ->when($request->search, function ($q) use ($request) {
                $like = \Illuminate\Support\Facades\DB::getDriverName() === 'pgsql' ? 'ilike' : 'like';
                $q->where('name', $like, "%{$request->search}%");
            })
            ->when($request->status === 'active', fn($q) => $q->active())
            ->when($request->status === 'inactive', fn($q) => $q->inactive())
            ->paginate(10)
            ->withQueryString();

        $stats = [
            'total' => DocumentType::count(),
            'active' => DocumentType::active()->count(),
            'most_requested' => DocumentType::withCount('documentRequests')
                                    ->orderByDesc('document_requests_count')
                                    ->first()?->name ?? 'None',
            'total_revenue' => \App\Models\DocumentRequest::where('status', 'released')
                                    ->join('document_types', 'document_requests.document_type_id', '=', 'document_types.id')
                                    ->sum('document_types.fee'),
        ];

        return view('document-types.index', compact('types', 'stats'));
    }

    public function create() {
        return view('document-types.create');
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name'            => 'required|string|max:150|unique:document_types,name',
            'category'        => 'required|string|in:Clearance,Certificate,Permit,ID,Other',
            'description'     => 'nullable|string|max:1000',
            'requirements'    => 'nullable|string',
            'fee'             => 'required|numeric|min:0',
            'processing_days' => 'required|integer|min:1',
        ]);

        $docType = DocumentType::create($validated);

        ActivityLog::record(
            action: 'created',
            subject: $docType,
            description: "Document type '{$docType->name}' was created by " . Auth::user()->name
        );

        return redirect()->route('document-types.index')->with('success', 'Document type added.');
    }

    public function edit(DocumentType $documentType) {
        return view('document-types.edit', compact('documentType'));
    }

    public function update(Request $request, DocumentType $documentType) {
        $validated = $request->validate([
            'name'            => 'required|string|max:150|unique:document_types,name,' . $documentType->id,
            'category'        => 'required|string|in:Clearance,Certificate,Permit,ID,Other',
            'description'     => 'nullable|string|max:1000',
            'requirements'    => 'nullable|string',
            'fee'             => 'required|numeric|min:0',
            'processing_days' => 'required|integer|min:1',
            'is_active'       => 'boolean',
        ]);

        $documentType->update($validated);

        ActivityLog::record(
            action: 'updated',
            subject: $documentType,
            description: "Document type '{$documentType->name}' was updated by " . Auth::user()->name
        );

        return redirect()->route('document-types.index')->with('success', 'Document type updated.');
    }

    public function toggle(DocumentType $documentType) {
        $documentType->update(['is_active' => !$documentType->is_active]);
        $status = $documentType->is_active ? 'activated' : 'deactivated';

        ActivityLog::record(
            action: 'updated',
            subject: $documentType,
            description: "Document type '{$documentType->name}' was {$status} by " . Auth::user()->name
        );

        return back()->with('success', "Document type \"{$documentType->name}\" has been {$status}.");
    }

    public function reorder(Request $request) {
        $validated = $request->validate([
            'order' => 'required|array',
            'order.*' => 'exists:document_types,id',
        ]);

        foreach ($validated['order'] as $index => $id) {
            DocumentType::where('id', $id)->update(['sort_order' => $index]);
        }

        ActivityLog::record(
            action: 'updated',
            subject: null,
            description: "Document types sort order was updated by " . Auth::user()->name
        );

        return response()->json(['success' => true]);
    }

    public function destroy(DocumentType $documentType) {
        // Safety guard: prevent cascade deletion of linked request history
        if ($documentType->documentRequests()->exists()) {
            return redirect()->route('document-types.index')
                ->with('error', 'Cannot delete "' . $documentType->name . '" because it has linked requests. Deactivate it instead.');
        }

        // Audit log before deletion
        ActivityLog::record(
            action: 'deleted',
            subject: $documentType,
            description: "Document type '{$documentType->name}' was deleted by " . Auth::user()->name
        );

        $documentType->delete();
        return redirect()->route('document-types.index')->with('success', 'Document type deleted.');
    }
}
