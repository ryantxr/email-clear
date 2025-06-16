<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImapAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email',
        'host',
        'port',
        'encryption',
        'username',
        'password',
    ];

    protected $casts = [
        'password' => 'encrypted',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
