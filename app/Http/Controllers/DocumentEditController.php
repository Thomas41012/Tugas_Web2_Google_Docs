<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\View\View;

class DocumentEditController extends Controller
{
    public function index(Document $document): View
    {
        $edits = $document->edits()
            ->with('user')
            ->orderByDesc('created_at')
            ->limit(200)
            ->get();

        return view('documents.activity', compact('document', 'edits'));
    }
}
