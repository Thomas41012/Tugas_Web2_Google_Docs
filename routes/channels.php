<?php

use App\Models\Document;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('document.{documentId}', function ($user, $documentId) {
    if (! Document::query()->whereKey($documentId)->exists()) {
        return false;
    }

    return [
        'id' => $user->id,
        'name' => $user->name,
    ];
});
