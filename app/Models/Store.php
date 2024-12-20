<?php

namespace App\Models;

use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Store extends Model
{
    use HasFactory, SoftDeletes;

    public $appends = ['is_active', 'check_stock'];

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
        $user = User::find($user->id);

        if ($user->role_id <= 4) {
            $purchases = $this->purchases()->whereBetween('created_at', [$startDate, $endDate]);
            $sales = $this->sales()->whereBetween('created_at', [$startDate, $endDate]);
            $salesItems = $this->sales_items()->whereBetween('created_at', [$startDate, $endDate]);
        } else {
            $purchases = $this->purchases()->whereBetween('created_at', [$startDate, $endDate])->where('user_id', $user->id);
            $sales = $this->sales()->whereBetween('created_at', [$startDate, $endDate])->where('user_id', $user->id);
            $salesItems = SalesItem::join('sales', 'sales.id', '=', 'sales_items.sales_id')
                ->where('sales_items.store_id', $this->id)
                ->whereBetween('sales_items.created_at', [$startDate, $endDate])
                ->where('sales.user_id', $user->id)
                ->select('sales_items.*');
        }

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

    public function checkStock()
    {
        $products = Product::where('store_id', $this->id)->where('disabled_at', null)->get();

        $stockRunningLow = 0;
        $outOfStock = 0;

        foreach ($products as $product) {

            if ($product->stock == 0) {
                $outOfStock++;
            } elseif ($product->stock <= 5) {
                $stockRunningLow++;
            }
        }

        return [
            'stock_running_low' => $stockRunningLow,
            'out_of_stock' => $outOfStock,
        ];
    }

    public function getIsActiveAttribute()
    {
        return $this->activated_at != null && $this->disabled_at == null;
    }

    public function getCheckStockAttribute()
    {
        return $this->checkStock();
    }

    public function lowStockProduct()
    {
        $products = Product::where('store_id', $this->id)->where('disabled_at', null)->get();
        $products = $products->where('stock', '<=', 5)->sortBy('stock');

        $data = [];

        foreach ($products as $product) {
            $data[] = $product;
        }

        return $data;
    }

    public function monthlySalesRevenueGraph($year)
    {
        $user = Auth::user();

        if ($user->role_id <= 4) {
            $sales = $this->sales()->whereYear('created_at', $year)->get();
        } else {
            $sales = $this->sales()->whereYear('created_at', $year)->where('user_id', $user->id)->get();
        }

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

        if ($user->role_id <= 4) {
            $sales = $this->sales()->whereYear('created_at', $year)->whereMonth('created_at', $month)->get();
        } else {
            $sales = $this->sales()->whereYear('created_at', $year)->whereMonth('created_at', $month)->where('user_id', $user->id)->get();
        }

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



        // Generate current week graph data
        if ($user->role_id <= 4) {
            $sales = $this->sales()->whereYear('created_at', $year)->whereMonth('created_at', $month)->get();
        } else {
            $sales = $this->sales()->whereYear('created_at', $year)->whereMonth('created_at', $month)->where('user_id', $user->id)->get();
        }

        $salesRevenue = $sales->groupBy(function ($item) {
            return $item->created_at->format('d');
        })->map(function ($item) {
            return $item->sum('total');
        });

        $days = [];
        $revenue = [];

        $start = Carbon::create($year, $month, 1);
        $end = Carbon::create($year, $month, 1)->endOfMonth();

        for ($i = $start; $i <= $end; $i->addDay()) {
            $days[] = $i->format('d');
            $revenue[] = $salesRevenue->get($i->format('d'), 0);
        }

        return [
            'year' => $year,
            'month' => $month,
            'week' => $week,
            'sales_revenue' => $salesRevenue,
            'labels' => $days,
            'revenue' => $revenue,
        ];
    }
}
