<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'subscription_id',
        'plan_history_id',
        'description',
        'amount',
        'due_at',
        'paid_at',
        'snap_token',
    ];

    protected $appends = ['status'];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function planHistory()
    {
        return $this->belongsTo(PlanHistory::class);
    }

    public function invoicePayments()
    {
        return $this->hasMany(InvoicePayment::class);
    }

    public function getStatusAttribute()
    {
        if ($this->paid_at == null && $this->due_at > now()) {
            return 'Pending';
        }

        if ($this->paid_at == null && $this->due_at < now()) {
            return 'Expired';
        }

        if ($this->paid_at) {
            return 'Paid';
        }

        return 'Unpaid';
    }
}
