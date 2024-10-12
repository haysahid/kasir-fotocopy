<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'note',
        'payment',
        'address',
        'user_id',
        'store_id',
    ];

    public function sales_items()
    {
        return $this->hasMany(SalesItem::class)->with('product');
    }

    public function total()
    {
        return $this->sales_items->sum(function ($item) {
            return $item->quantity * $item->item_price;
        });
    }
}
