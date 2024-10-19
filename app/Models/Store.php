<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

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
        'is_community',
        'community_id',
        'activated_at',
        'disabled_at',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_stores');
    }

    public function owners()
    {
        return $this->belongsToMany(User::class, 'user_stores')->where('role_id', 4);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function purchase_items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function sales_items()
    {
        return $this->hasMany(SalesItem::class);
    }

    public function summary($startDate, $endDate)
    {
        $user = Auth::user();

        $purchases = $this->purchases()->whereBetween('created_at', [$startDate, $endDate])->where('user_id', $user->id);
        $sales = $this->sales()->whereBetween('created_at', [$startDate, $endDate])->where('user_id', $user->id);
        $salesItems = $this->sales_items()->whereBetween('created_at', [$startDate, $endDate])->where('user_id', $user->id);

        $totalPurchases = 0;

        if ($purchases->count() > 0) {
            $purchaseItems = PurchaseItem::whereIn('purchase_id', $purchases->pluck('id'))->get();
            $totalPurchases = $purchaseItems->sum(function ($item) {
                return $item->quantity * $item->item_price;
            });
        }

        $totalSales = 0;

        if ($sales->count() > 0) {
            $salesItems = SalesItem::whereIn('sales_id', $sales->pluck('id'))->get();
            $totalSales = $salesItems->sum(function ($item) {
                return $item->quantity * $item->item_price;
            });
        }

        $totalPurchasesCount = $purchases->count();
        $totalSalesCount = $sales->count();
        $soldProducts = $salesItems->sum('quantity');

        return [
            'total_purchases' => $totalPurchases,
            'total_sales' => $totalSales,
            'total_purchases_count' => $totalPurchasesCount,
            'total_sales_count' => $totalSalesCount,
            'mean_purchases' => $totalPurchasesCount == 0 ? 0 : $totalPurchases / $totalPurchasesCount,
            'mean_sales' => $totalSalesCount == 0 ? 0 : $totalSales / $totalSalesCount,
            'sold_products' => $soldProducts,
        ];
    }

    public function monthlySalesRevenueGraph($year)
    {
        $user = Auth::user();

        $sales = $this->sales()->whereYear('created_at', $year)->where('user_id', $user->id)->get();

        $salesRevenue = $sales->groupBy(function ($item) {
            return $item->created_at->format('m');
        })->map(function ($item) {
            return $item->sum('total');
        });

        $months = [];
        $revenue = [];

        for ($i = 1; $i <= 12; $i++) {
            $months[] = DateTime::createFromFormat('!m', $i)->format('F');
            $revenue[] = $salesRevenue->get($i, 0);
        }

        return [
            'labels' => $months,
            'revenue' => $revenue,
        ];
    }

    public function weeklyMonthlySalesRevenueGraph($year, $month)
    {
        $user = Auth::user();

        $sales = $this->sales()->whereYear('created_at', $year)->whereMonth('created_at', $month)->where('user_id', $user->id)->get();

        $salesRevenue = $sales->groupBy(function ($item) {
            return $item->created_at->format('W');
        })->map(function ($item) {
            return $item->sum('total');
        });

        $weeks = [];
        $revenue = [];

        for ($i = 1; $i <= 5; $i++) {
            $weeks[] = strval($i);
            $revenue[] = $salesRevenue->get($i, 0);
        }

        return [
            'labels' => $weeks,
            'revenue' => $revenue,
        ];
    }

    public function dailySalesRevenueGraph($year, $month, $week)
    {
        $user = Auth::user();

        $sales = $this->sales()->whereYear('created_at', $year)->whereMonth('created_at', $month)->whereRaw('WEEK(created_at) = ' . date($week))->where('user_id', $user->id)->get();

        $salesRevenue = $sales->groupBy(function ($item) {
            return $item->created_at->format('d');
        })->map(function ($item) {
            return $item->sum('total');
        });

        $days = [];
        $revenue = [];

        for ($i = 1; $i <= 7; $i++) {
            $days[] = DateTime::createFromFormat('!d', $i)->format('D');
            $revenue[] = $salesRevenue->get($i, 0);
        }

        return [
            'labels' => $days,
            'revenue' => $revenue,
        ];
    }
}
