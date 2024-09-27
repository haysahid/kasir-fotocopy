<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'address',
        'phone',
        'logo',
        'banner',
        'community_id',
        'activated_at',
        'disabled_at',
    ];

    public function user()
    {
        return $this->belongsToMany(User::class, 'user_stores');
    }
}
