<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DocumentManagementController extends Controller
{
    public function index(Request $request): View
    {
        $query = Document::with('application.customer')->latest();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $documents = $query->paginate(15);

        return view('admin.documents.index', [
            'documents' => $documents,
            'statuses' => ['uploaded', 'pending review', 'approved', 'rejected'],
        ]);
    }

    public function review(Document $document): View
    {
        return view('admin.documents.review', ['document' => $document]);
    }

    public function approve(Request $request, Document $document): RedirectResponse
    {
        $document->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'review_note' => $request->review_note,
        ]);

        return redirect()->route('admin.documents.index')->with('success', 'Document approved');
    }

    public function reject(Request $request, Document $document): RedirectResponse
    {
        $validated = $request->validate([
            'review_note' => ['required', 'string', 'max:500'],
        ]);

        $document->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'review_note' => $validated['review_note'],
        ]);

        return redirect()->route('admin.documents.index')->with('success', 'Document rejected');
    }
}
