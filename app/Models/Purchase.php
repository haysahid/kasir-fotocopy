<?php

namespace App\Models;

use Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use HasFactory, SoftDeletes;

    protected $appends = array('total', 'return');

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

    public function purchase_items()
    {
        return $this->hasMany(PurchaseItem::class)->with('product');
    }

    public function getTotalAttribute()
    {
        return $this->purchase_items->sum(function ($item) {
            return $item->quantity * $item->item_price;
        });
    }

    public function getReturnAttribute()
    {
        return $this->payment - $this->total;
    }
}
