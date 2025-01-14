<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Dompdf\Dompdf;

class ReportController extends Controller
{
    // Get purchase report
    public function purchase(Request $request)
    {
        $user = Auth::user();
        $user = User::find($user->id);
        $store = $user->store[0];

        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        // Validate date
        try {
            new DateTime($startDate);
            new DateTime($endDate);
        } catch (Exception $error) {
            return ResponseFormatter::error('Format tanggal tidak valid.', 400);
        }

        $purchases = $store->purchases()->with('purchase_items')
            ->whereBetween('created_at', [$startDate, $endDate . ' 23:59:59'])
            ->get();

        $total = $purchases->sum('total');

        return ResponseFormatter::success([
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total' => $total,
            'purchases' => $purchases,
        ], 'Data laporan pembelian berhasil diambil.');
    }

    // Get sales report
    public function sales(Request $request)
    {
        $user = Auth::user();
        $user = User::find($user->id);
        $store = $user->store[0];

        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        // Validate date
        try {
            new DateTime($startDate);
            new DateTime($endDate);
        } catch (Exception $error) {
            return ResponseFormatter::error('Format tanggal tidak valid.', 400);
        }

        $sales = $store->sales()->with('sales_items')
            ->whereBetween('created_at', [$startDate, $endDate . ' 23:59:59'])
            ->get();

        $total = $sales->sum('total');
        $totalProfit = $sales->sum('profit');

        return ResponseFormatter::success([
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total' => $total,
            'total_profit' => $totalProfit,
            'sales' => $sales,
        ], 'Data laporan penjualan berhasil diambil.');
    }

    // Generate PDF
    public function generatePdf(Request $request)
    {
        $content = $request->input('content');

        $dompdf = new Dompdf();
        $dompdf->loadHtml($content);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $pdf = base64_encode($dompdf->output());

        return ResponseFormatter::success([
            'pdf' => $pdf,
        ], 'PDF berhasil di-generate.');
    }
}
