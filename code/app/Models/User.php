<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\UserToken;
use App\Models\Setting;

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
        'monthly_scanned',
        'scan_month',
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
            'monthly_scanned' => 'integer',
            'scan_month' => 'string',
        ];
    }


    public function plan(): string
    {
        return $this->plan ?? 'free';
    }

    public function planLimits(): array
    {
        $db = Setting::where('key', 'plans')->value('value');
        $plans = json_decode($db, true);
        if (!is_array($plans)) {
            $plans = config('plans');
        }
        return $plans[$this->plan()] ?? ($plans['free'] ?? []);
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

    /**
     * IMAP accounts associated with the user.
     */
    public function imapAccounts()
    {
        return $this->hasMany(ImapAccount::class);
    }

    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    public function emailCount(): int
    {
        return $this->tokens()->count() + $this->imapAccounts()->count();
    }

    public function canAddEmail(): bool
    {
        return $this->emailCount() < $this->planLimit('max_tokens', PHP_INT_MAX);
    }

    public function canScanMore(): bool
    {
        $limit = $this->planLimit('monthly_limit', PHP_INT_MAX);
        $month = now()->format('Y-m');
        if ($this->scan_month !== $month) {
            return true;
        }
        return $this->monthly_scanned < $limit;
    }

    public function incrementMonthlyScanned(int $amount): void
    {
        $month = now()->format('Y-m');
        if ($this->scan_month !== $month) {
            $this->scan_month = $month;
            $this->monthly_scanned = 0;
        }
        $this->monthly_scanned += $amount;
        $this->save();
    }
}
