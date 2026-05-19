<?php

namespace App\Http\Controllers;

use App\Events\CursorMoved;
use App\Events\DocumentContentUpdated;
use App\Models\Document;
use App\Models\DocumentEdit;
use App\Models\DocumentRevision;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DocumentController extends Controller
{
    public function index(): View
    {
        $documents = Document::with('owner')
            ->latest('updated_at')
            ->get();

        return view('documents.index', compact('documents'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);

        $document = Document::create([
            'user_id' => Auth::id(),
            'title' => $data['title'],
            'content' => '<p>Mulai mengetik di sini...</p>',
            'version' => 1,
        ]);

        DocumentRevision::create([
            'document_id' => $document->id,
            'user_id' => Auth::id(),
            'version_number' => 1,
            'content' => $document->content,
            'change_summary' => 'Dokumen dibuat',
        ]);

        return redirect()->route('documents.show', $document);
    }

    public function show(Document $document): View
    {
        return view('documents.show', [
            'document' => $document,
            'user' => Auth::user(),
            'userColor' => $this->userColor(Auth::id()),
        ]);
    }

    public function sync(Request $request, Document $document): JsonResponse
    {
        $data = $request->validate([
            'content' => ['required', 'string'],
            'version' => ['required', 'integer', 'min:1'],
            'caret_position' => ['nullable', 'integer', 'min:0'],
            'action' => ['nullable', 'string', 'max:50'],
        ]);

        $user = Auth::user();
        $clientVersion = (int) $data['version'];
        $hadConflict = false;
        $conflictWith = null;

        $result = DB::transaction(function () use ($document, $data, $user, $clientVersion, &$hadConflict, &$conflictWith) {
            $locked = Document::query()->lockForUpdate()->findOrFail($document->id);

            if ($clientVersion < $locked->version) {
                $hadConflict = true;
                $lastEdit = DocumentEdit::query()
                    ->with('user')
                    ->where('document_id', $locked->id)
                    ->where('user_id', '!=', $user->id)
                    ->latest()
                    ->first();

                $conflictWith = $lastEdit ? [
                    'user_id' => $lastEdit->user_id,
                    'user_name' => $lastEdit->user?->name,
                    'version' => $lastEdit->document_version,
                    'at' => $lastEdit->created_at?->toIso8601String(),
                ] : null;
            }

            $locked->content = $data['content'];
            $locked->version = $locked->version + 1;
            $locked->save();

            $snippet = mb_substr(strip_tags($data['content']), 0, 120);

            DocumentEdit::create([
                'document_id' => $locked->id,
                'user_id' => $user->id,
                'action' => $data['action'] ?? 'sync',
                'document_version' => $locked->version,
                'caret_position' => $data['caret_position'] ?? null,
                'snippet' => $snippet,
                'had_conflict' => $hadConflict,
                'conflict_with' => $conflictWith,
            ]);

            if ($locked->version % 5 === 0) {
                DocumentRevision::create([
                    'document_id' => $locked->id,
                    'user_id' => $user->id,
                    'version_number' => $locked->version,
                    'content' => $locked->content,
                    'change_summary' => 'Auto-save versi '.$locked->version,
                ]);
            }

            return $locked->fresh();
        });

        broadcast(new DocumentContentUpdated(
            $result,
            $user,
            $result->content,
            $result->version,
            $data['caret_position'] ?? null,
        ))->toOthers();

        return response()->json([
            'ok' => true,
            'version' => $result->version,
            'content' => $result->content,
            'had_conflict' => $hadConflict,
            'conflict_with' => $conflictWith,
        ]);
    }

    public function cursor(Request $request, Document $document): JsonResponse
    {
        $data = $request->validate([
            'caret_position' => ['required', 'integer', 'min:0'],
        ]);

        $user = Auth::user();
        $color = $this->userColor($user->id);

        broadcast(new CursorMoved(
            $document,
            $user,
            $data['caret_position'],
            $color,
        ))->toOthers();

        return response()->json(['ok' => true]);
    }

    public function destroy(Document $document): RedirectResponse
    {
        $document->delete();

        return redirect()->route('documents.index');
    }

    private function userColor(int $userId): string
    {
        $colors = [
            '#e74c3c', '#3498db', '#2ecc71', '#9b59b6',
            '#f39c12', '#1abc9c', '#e91e63', '#00bcd4',
        ];

        return $colors[$userId % count($colors)];
    }
}
