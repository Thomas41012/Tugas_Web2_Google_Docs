<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentEdit;
use App\Models\DocumentRevision;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DocumentRevisionController extends Controller
{
    public function index(Document $document): View
    {
        $revisions = $document->revisions()
            ->with('user')
            ->orderByDesc('version_number')
            ->get();

        return view('documents.history', compact('document', 'revisions'));
    }

    public function show(Document $document, DocumentRevision $revision): View
    {
        abort_unless($revision->document_id === $document->id, 404);

        return view('documents.revision', compact('document', 'revision'));
    }

    public function restore(Document $document, DocumentRevision $revision): RedirectResponse
    {
        abort_unless($revision->document_id === $document->id, 404);

        DB::transaction(function () use ($document, $revision) {
            $document->content = $revision->content;
            $document->version = $document->version + 1;
            $document->save();

            DocumentRevision::create([
                'document_id' => $document->id,
                'user_id' => Auth::id(),
                'version_number' => $document->version,
                'content' => $document->content,
                'change_summary' => 'Restore dari versi '.$revision->version_number,
            ]);

            DocumentEdit::create([
                'document_id' => $document->id,
                'user_id' => Auth::id(),
                'action' => 'restore',
                'document_version' => $document->version,
                'snippet' => 'Restore versi '.$revision->version_number,
                'had_conflict' => false,
            ]);
        });

        return redirect()
            ->route('documents.show', $document)
            ->with('status', 'Versi '.$revision->version_number.' berhasil dipulihkan.');
    }
}
