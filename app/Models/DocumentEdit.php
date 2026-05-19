<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentEdit extends Model
{
    protected $fillable = [
        'document_id',
        'user_id',
        'action',
        'document_version',
        'caret_position',
        'snippet',
        'had_conflict',
        'conflict_with',
    ];

    protected function casts(): array
    {
        return [
            'document_version' => 'integer',
            'caret_position' => 'integer',
            'had_conflict' => 'boolean',
            'conflict_with' => 'array',
        ];
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
