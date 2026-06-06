<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use SoftDeletes;

    protected $fillable = ['match_id', 'sender_id', 'body', 'read_at', 'edited_at'];

    protected $casts = [
        'read_at'   => 'datetime',
        'edited_at' => 'datetime',
    ];

    public function match(): BelongsTo
    {
        return $this->belongsTo(UserMatch::class, 'match_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function markAsRead()
    {
        if (is_null($this->read_at)) {
            $this->update(['read_at' => now()]);
        }
    }

    public function isEditable(): bool
    {
        if ($this->trashed()) return false;
        if ($this->sender_id !== auth()->id()) return false;

        $maxEditMinutes = 30;
        return $this->created_at->diffInMinutes(now()) < $maxEditMinutes;
    }

    public function isDeletable(): bool
    {
        if ($this->trashed()) return false;
        if ($this->sender_id !== auth()->id()) return false;

        $maxDeleteMinutes = 60;
        return $this->created_at->diffInMinutes(now()) < $maxDeleteMinutes;
    }
}
