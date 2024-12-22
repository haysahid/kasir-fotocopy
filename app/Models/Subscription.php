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
        'invoice_id',
        'duration_text',
        'amount',
        'plan',
        'status',
    ];

    protected $casts = [
        'subscribe_after_trial' => 'boolean',
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

    public function getStatusAttribute()
    {
        $latestInvoice = $this->invoices()->latest()->first();

        if ($latestInvoice && $latestInvoice->status === 'Pending') {
            return 'Pending Payment';
        }

        if ($latestInvoice && $latestInvoice->status !== 'Paid') {
            return 'Unpaid';
        }

        if ($this->date_unsubscribed) {
            return 'Unsubscribed';
        }

        if ($this->valid_to < now()) {
            return 'Expired';
        }

        return 'Active';
    }

    public function getInvoiceIdAttribute()
    {
        return $this->invoices()->latest()->first()->id;
    }

    public function getAmountAttribute()
    {
        return $this->invoices()->latest()->first()->amount;
    }

    public function getDurationTextAttribute()
    {
        $planHistory = $this->planHistories()->latest()->first();
        $plan = $planHistory->plan;

        $quantity = $planHistory->quantity;
        $durationUnit = '';

        if ($plan->duration_type === 'days') {
            $durationUnit = 'hari';
        } elseif ($plan->duration_type === 'weeks') {
            $durationUnit = 'minggu';
        } elseif ($plan->duration_type === 'months') {
            $durationUnit = 'bulan';
        } elseif ($plan->duration_type === 'years') {
            $durationUnit = 'tahun';
        }

        return $quantity . ' ' . $durationUnit;
    }
}
