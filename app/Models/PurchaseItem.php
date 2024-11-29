<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $appends = array('sub_total');

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'purchase_id',
        'product_id',
        'quantity',
        'item_price',
        'store_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getSubTotalAttribute()
    {
        return $this->quantity * $this->item_price;
    }
}
