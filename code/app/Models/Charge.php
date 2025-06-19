<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Charge
 *
 * @property int $id
 * @property int $user_id
 * @property int $plan_id
 * @property float $amount
 * @property string $status
 */
class Charge extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'plan_id',
        'amount',
        'status',
    ];

    /**
     * Get the user that owns the charge.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the plan associated with the charge.
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
