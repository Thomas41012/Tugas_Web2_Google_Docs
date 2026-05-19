<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Document extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'content',
        'version',
    ];

    protected function casts(): array
    {
        return [
            'version' => 'integer',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(DocumentRevision::class)->orderByDesc('version_number');
    }

    public function edits(): HasMany
    {
        return $this->hasMany(DocumentEdit::class)->orderByDesc('created_at');
    }
}
