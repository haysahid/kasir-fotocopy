<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $appends = ['purchased', 'sold', 'stock'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'name',
        'description',
        'purchase_price',
        'selling_price',
        'initial_stock',
        'unit',
        'category',
        'from_community',
        'store_id',
        'disabled_at',
    ];

    // Hide attributes
    protected $hidden = [
        'purchase_items',
        'sales_items',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function product_images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function purchase_items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function sales_items()
    {
        return $this->hasMany(SalesItem::class);
    }

    public function getPurchasedAttribute()
    {
        return $this->purchase_items->sum('quantity');
    }

    public function getSoldAttribute()
    {
        return $this->sales_items->sum('quantity');
    }

    public function getStockAttribute()
    {
        return $this->initial_stock + $this->purchased - $this->sold;
    }

    public function stock()
    {
        return $this->initial_stock + $this->purchased - $this->sold;
    }
}
