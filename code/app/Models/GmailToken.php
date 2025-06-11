<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GmailToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'token',
        'daily_count',
        'daily_count_date',
    ];

    protected $casts = [
        'token' => 'array',
        'daily_count_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
