<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_id',
        'option_id',
        'date_added',
        'date_removed',
    ];
}
