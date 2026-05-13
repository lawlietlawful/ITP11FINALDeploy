<?php

namespace App\Http\Controllers;

use App\Models\DocumentType;
use Illuminate\Http\Request;

class DocumentTypeController extends Controller {

    public function index() {
        $types = DocumentType::latest()->paginate(10);
        return view('document-types.index', compact('types'));
    }

    public function create() {
        return view('document-types.create');
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name'            => 'required|string|max:150|unique:document_types,name',
            'description'     => 'nullable|string|max:1000',
            'fee'             => 'required|numeric|min:0',
            'processing_days' => 'required|integer|min:1',
        ]);

        DocumentType::create($validated);
        return redirect()->route('document-types.index')->with('success', 'Document type added.');
    }

    public function edit(DocumentType $documentType) {
        return view('document-types.edit', compact('documentType'));
    }

    public function update(Request $request, DocumentType $documentType) {
        $validated = $request->validate([
            'name'            => 'required|string|max:150|unique:document_types,name,' . $documentType->id,
            'description'     => 'nullable|string|max:1000',
            'fee'             => 'required|numeric|min:0',
            'processing_days' => 'required|integer|min:1',
            'is_active'       => 'boolean',
        ]);

        $documentType->update($validated);
        return redirect()->route('document-types.index')->with('success', 'Document type updated.');
    }

    public function destroy(DocumentType $documentType) {
        $documentType->delete();
        return redirect()->route('document-types.index')->with('success', 'Document type deleted.');
    }
}
