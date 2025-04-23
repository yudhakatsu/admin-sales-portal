@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <!-- Tambahkan Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            background-color: #FCC6FF;
        }

        .container {
            margin-top: 20px;
        }

        .card {
            background-color: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            color: #7a007a;
        }

        .table {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
        }

        .table th {
            background-color: #FCC6FF;
            color: white;
        }

        .table tbody tr:hover {
            background-color: #f3d9ff;
        }

        #salesChartContainer {
            background-color: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>

    <div class="container">
        <h2 class="text-center text-white">Dashboard</h2>

        <div class="row mt-4">
            <!-- Kartu untuk informasi penjualan -->
            <div class="col-md-6">
                <div class="card">
                    <h4>Penjualan Bulanan</h4>
                    <h3>Rp {{ number_format($monthlySales, 0, ',', '.') }}</h3>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <h4>Produk Terjual</h4>
                    <h3>{{ number_format($productsSold) }} pcs</h3>
                </div>
            </div>
        </div>

        <!-- Chart Penjualan -->
        <div class="mt-4" id="salesChartContainer">
            <h4 class="text-center">Grafik Penjualan</h4>
            <canvas id="salesChart" style="max-width: 400px; max-height: 300px; margin: auto;"></canvas>
        </div>

        <!-- Tabel Laporan Transaksi -->
        <div class="mt-4">
            <h4 class="text-white">Laporan Transaksi</h4>
            <table class="table table-bordered table-hover">
                <thead>
                    <tr class="text-center">
                        <th>ID</th>
                        <th>Nama Customer</th>
                        <th>Tanggal</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transactions as $transaction)
                        <tr class="text-center">
                            <td>{{ $transaction->id }}</td>
                            <td>{{ $transaction->customer_name }}</td>
                            <td>{{ \Carbon\Carbon::parse($transaction->order_date)->format('d M Y') }}</td>
                            <td>Rp {{ number_format($transaction->total, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('salesChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json(array_keys($salesData)), // Data label (bulan/tanggal)
                    datasets: [{
                        label: 'Total Penjualan',
                        data: @json(array_values($salesData)), // Data nilai
                        backgroundColor: '#FCC6FF',
                        borderColor: '#7a007a',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false, // Supaya bisa disesuaikan ukurannya
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
@endsection
