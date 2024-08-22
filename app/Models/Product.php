<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'capital_price',
        'selling_price',
        'stock',
        'unit',
        'category',
        'store_id',
        'disabled_at',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function product_images()
    {
        return $this->hasMany(ProductImage::class);
    }
}
