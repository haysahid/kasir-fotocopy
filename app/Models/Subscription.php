<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'trial_periode_start',
        'trial_periode_end',
        'subscribe_after_trial',
        'date_subscribed',
        'valid_to',
        'date_unsubscribed',
    ];

    protected $appends = [
        'plan',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function planHistories()
    {
        return $this->hasMany(PlanHistory::class);
    }

    public function getPlanAttribute()
    {
        $planHistory = $this->planHistories()->latest()->first();

        return $planHistory ? $planHistory->plan : null;
    }
}
