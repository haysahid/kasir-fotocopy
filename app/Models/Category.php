<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $appends = ['total_products'];

    protected $fillable = [
        'name',
        'description',
        'image',
        'store_id',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_categories');
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function getTotalProductsAttribute()
    {
        return $this->products()->count();
    }
}
