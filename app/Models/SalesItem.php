<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesItem extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sales_id',
        'product_id',
        'quantity',
        'item_price',
        'store_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class)->with(['store', 'product_images', 'tags']);
    }
}
