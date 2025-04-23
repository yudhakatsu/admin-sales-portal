@extends('layouts.app')

@section('title', 'Customers Data')

@section('content')
    <div class="container">
        <h2>Daftar Customers</h2>
        <!-- <a href="{{ route('transactions.create') }}" class="btn btn-primary">Tambah Transaksi</a> -->
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Alamat</th>
                    <th>No HP</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($customers as $customer)
                    <tr>
                        <td>{{ $customer->id }}</td>
                        <td>{{ $customer->name }}</td>
                        <td>{{ $customer->address }}</td>
                        <td>{{ $customer->phone_number }}</td>
                        <td>
                            <a href="#" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#exampleModalToggle{{ $customer->id }}">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-warning">Edit</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal untuk menampilkan transaksi dan detail -->
    @foreach ($customers as $customer)
        <div class="modal fade" id="exampleModalToggle{{ $customer->id }}" aria-hidden="true" aria-labelledby="exampleModalToggleLabel" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalToggleLabel">Detail Transaksi untuk {{ $customer->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="info d-flex flex-row justify-content-between mx-5">
                            <label for="product_buy" class="form-label">Nama Customer: </label>
                            <label for="total_buy" class="form-label">{{ $customer->name }}</label>
                        </div>
                        <div class="info d-flex flex-row justify-content-between mx-5">
                            <label for="product_buy" class="form-label">Alamat: </label>
                            <label for="total_buy" class="form-label">{{ $customer->address }}</label>
                        </div>
                        
                        <div class="detail-info">
                            <h4>Daftar Transaksi</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Produk</th>
                                        <th>Jumlah</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($customer->transactions as $transaction)
                                        @foreach ($transaction->details as $detail)
                                            <tr>
                                                <td>{{ $transaction->order_date }}</td>
                                                <td>{{ $detail->product->name }}</td>
                                                <td>{{ $detail->quantity }}</td>
                                                <td>{{ $detail->subtotal }}</td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" data-bs-target="#exampleModalToggle{{ $customer->id }}" data-bs-toggle="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <style>
        /* Warna Utama */
        :root {
            --main-color: #FCC6FF;
            --dark-color: #222222;
            --danger-color: #ff6b6b;
            --border-color: #ddd;
            --hover-bg: #f0f0f0;
        }

        /* Container utama */
        .container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 2px 4px 10px rgba(0, 0, 0, 0.1);
        }

        /* Tabel */
        .table {
            margin-top: 20px;
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
        }

        .table th,
        .table td {
            border: 1px solid var(--border-color);
            padding: 10px;
            text-align: center;
        }

        .table th {
            background-color: var(--main-color);
            color: var(--dark-color);
            font-weight: bold;
        }

        /* Garis lebih tebal untuk header */
        .table thead tr {
            border-bottom: 2px solid var(--main-color);
        }

        /* Hover efek pada baris */
        .table tbody tr:hover {
            background-color: var(--hover-bg);
            transition: 0.3s;
        }

        /* Produk - Tata letak lebih rapi */
        .product-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
            align-items: flex-start;
        }

        /* Setiap item produk */
        .product-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Gambar produk */
        .product-item img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 5px;
            box-shadow: 1px 2px 5px rgba(0, 0, 0, 0.1);
        }

        /* Info produk */
        .product-info {
            display: flex;
            flex-direction: column;
            text-align: left;
            max-width: 150px;
            word-wrap: break-word;
        }

        .product-name {
            font-weight: bold;
            font-size: 14px;
        }

        .product-qty {
            font-size: 13px;
            color: #555;
        }

        /* Tombol */
        .btn-primary {
            background-color: var(--main-color);
            border: none;
            color: black;
            font-weight: bold;
            padding: 8px 12px;
            border-radius: 5px;
            transition: 0.3s;
        }

        .btn-primary:hover {
            background-color: #e6b3e6;
        }

        .btn-danger {
            background-color: var(--danger-color);
            border: none;
            color: white;
            font-weight: bold;
            padding: 8px 12px;
            border-radius: 5px;
            transition: 0.3s;
        }

        .btn-danger:hover {
            background-color: #e55b5b;
        }

        /* Container untuk tombol agar bertumpuk secara vertikal */
        .action-buttons {
            display: flex;
            flex-direction: column;
            /* Membuat tombol bertumpuk ke bawah */
            gap: 5px;
            /* Jarak antar tombol */
            align-items: center;
            /* Agar sejajar di tengah */
        }

        /* Pastikan semua tombol memiliki ukuran yang sama */
        .action-buttons .btn {
            width: 100%;
            /* Semua tombol mengikuti ukuran terbesar */
            min-width: 100px;
            /* Sesuaikan dengan ukuran tombol terbesar */
            min-height: 40px;
            /* Sesuaikan dengan tinggi tombol terbesar */
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
        }


        /* Tombol lebih kecil */
        .btn {
            font-size: 12px;
            padding: 6px 10px;
        }

        /* Gunakan hanya ikon di layar kecil */
        @media (max-width: 768px) {
            .btn span {
                display: none;
                /* Sembunyikan teks, hanya ikon */
            }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(button) {
            Swal.fire({
                title: "Apakah Anda yakin?",
                text: "Transaksi ini akan dihapus secara permanen!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#6c757d",
                confirmButtonText: "Ya, Hapus!",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    button.closest('form').submit();
                }
            });
        }
    </script>
@endsection
