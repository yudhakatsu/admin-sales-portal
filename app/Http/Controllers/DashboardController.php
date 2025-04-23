<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil total penjualan bulan ini
        $monthlySales = Transaction::whereMonth('order_date', now()->month)->sum('total');

        // Ambil jumlah produk terjual bulan ini
        $productsSold = Transaction::whereMonth('order_date', now()->month)->sum('quantity');

        // Ambil transaksi terbaru (misal 10 transaksi terakhir)
        $transactions = Transaction::latest()->take(10)->get();

        // Ambil data penjualan untuk Chart.js
        $salesData = Transaction::selectRaw('DATE(order_date) as date, SUM(total) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date')
            ->toArray();

        return view('dashboard', compact('monthlySales', 'productsSold', 'transactions', 'salesData'));
    }
}
