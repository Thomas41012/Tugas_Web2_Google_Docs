<?php

namespace App\Events;

use App\Models\Document;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DocumentContentUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Document $document,
        public User $user,
        public string $content,
        public int $version,
        public ?int $caretPosition = null,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PresenceChannel('document.'.$this->document->id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'content.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'document_id' => $this->document->id,
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'content' => $this->content,
            'version' => $this->version,
            'caret_position' => $this->caretPosition,
        ];
    }
}
