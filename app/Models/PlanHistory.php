<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlanHistory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'subscription_id',
        'plan_id',
        'quantity',
        'date_start',
        'date_end',
    ];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class)->with('options');
    }
}
