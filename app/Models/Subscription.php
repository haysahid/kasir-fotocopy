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

    public function user() {
        return $this->belongsTo(User::class);
    }
}
