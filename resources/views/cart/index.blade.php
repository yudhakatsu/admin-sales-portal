@extends('layouts.app')

@section('title', 'Transaksi')

@section('content')
    <div class="container">
        <h2>Daftar Keranjang</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Customer</th>
                    <th>No HP</th>
                    <th>Alamat</th>
                    <th>Total (Rp)</th>
                    <th>Produk</th>
                    <th>Catatan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cart as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->customer->name }}</td>
                        <td>{{ $item->customer->phone_number }}</td>
                        <td>{{ $item->customer->address }}</td>
                        <td>{{ number_format($item->total, 2, ',', '.') }}</td>
                        <td>
                            <ul>
                                @foreach ($item->details as $detail)
                                    <li>{{ $detail->product->name }} ({{ $detail->quantity }}x)</li>
                                @endforeach
                            </ul>
                        </td>
                        <td>{{ $item->note ?? '-' }}</td>
                        <td>
                            <div class="action-button">
                                <a href="{{ route('cart.checkout', $item->id) }}" class="btn btn-info">
                                    <i class="fa-solid fa-shopping-cart"></i> Checkout
                                </a>
                                <br>
                                <a href="{{ route('cart.edit', $item->id) }}" class="btn btn-success">
                                        <i class="fa-solid fa-pen"></i> Edit
                                    </a>
                                <form action="{{ route('cart.destroy', $item->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus?')">
                                        <i class="fa-solid fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

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
        }

        .table th{
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
            margin-bottom: 10px;
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
