<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\UserToken;
use App\Models\ImapAccount;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'plan',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }


    public function plan(): string
    {
        return $this->plan ?? 'free';
    }

    public function planLimits(): array
    {
        $plans = config('plans');
        return $plans[$this->plan()] ?? $plans['free'] ?? [];
    }

    public function planLimit(string $key, $default = null)
    {
        return $this->planLimits()[$key] ?? $default;
    }

    /**
     * Get all OAuth tokens associated with the user.
     */
    public function tokens()
    {
        return $this->hasMany(UserToken::class);
    }

    public function imapAccounts()
    {
        return $this->hasMany(ImapAccount::class);
    }

    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }
}
