<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'path',
        'public_id',
        'sort_order',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}