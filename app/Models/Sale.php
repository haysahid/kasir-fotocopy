<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use HasFactory, SoftDeletes;

    protected $appends = array('total', 'return', 'profit', 'status');

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
        'type',
        'user_id',
        'store_id',
    ];

    public function sales_items()
    {
        return $this->hasMany(SalesItem::class, 'sales_id')->with('product');
    }

    public function total()
    {
        return $this->sales_items->sum(function ($item) {
            return $item->quantity * $item->item_price;
        });
    }

    public function getTotalAttribute()
    {
        return $this->total();
    }

    public function getReturnAttribute()
    {
        return $this->payment - $this->total;
    }

    public function getProfitAttribute()
    {
        if ($this->return < 0) {
            return $this->return;
        }

        return $this->sales_items->sum('profit');
    }

    public function status()
    {
        return $this->return >= 0 ? 'Lunas' : 'Belum Lunas';
    }

    public function getStatusAttribute()
    {
        return $this->status();
    }
}
