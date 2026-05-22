<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Document;
use App\Traits\AuditLoggingTrait;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    use AuditLoggingTrait;

    public function index(Request $request)
    {
        $query = Document::query()->latest();

        if ($request->user()->role !== 'admin') {
            $query->whereHas('application', function ($builder) use ($request) {
                $builder->where('agent_user_id', $request->user()->id);
            });
        }

        $documents = $query->paginate(20);

        return response()->json($documents);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'application_id' => ['required', 'integer', 'exists:applications,id'],
            'document_type' => ['required', 'string', 'max:255'],
            'file' => ['required', 'file', 'max:5120'],
        ]);

        $application = Application::findOrFail($validated['application_id']);
        if ($request->user()->role !== 'admin' && $application->agent_user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $storedPath = $validated['file']->store('documents', 'public');

        $document = Document::create([
            'application_id' => $validated['application_id'],
            'document_type' => $validated['document_type'],
            'file_path' => $storedPath,
            'status' => 'uploaded',
        ]);

        self::logAudit('document_uploaded', 'Document', $document->id, [
            'application_id' => $application->id,
            'document_type' => $validated['document_type'],
            'file_path' => $storedPath,
        ]);

        return response()->json($document, 201);
    }

    public function review(Request $request, Document $document)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:approved,rejected'],
            'review_note' => ['nullable', 'string'],
        ]);

        $document->update([
            'status' => $validated['status'],
            'review_note' => $validated['review_note'] ?? null,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        self::logAudit('document_reviewed', 'Document', $document->id, [
            'status' => $validated['status'],
            'review_note' => $validated['review_note'] ?? null,
        ]);

        return response()->json($document);
    }
}
