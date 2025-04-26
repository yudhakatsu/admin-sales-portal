@extends('layouts.app')

@section('title', 'Transaksi')

@section('content')
    <div class="container">
        <h2>Daftar Transaksi</h2>
        <a href="{{ route('transactions.create') }}" class="btn btn-primary">Tambah Transaksi</a>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Customer</th>
                    <th>Tanggal</th>
                    <th>Total</th>
                    <th>Status Pembayaran</th>
                    <th>Nominal DP</th>
                    <th>Kekurangan Bayar</th>
                    <th>Produk</th>
                    <th>Catatan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transactions as $transaction)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $transaction->customer->name ?? '-' }}</td>
                        <td>{{ $transaction->order_date }}</td>
                        <td>Rp {{ number_format($transaction->total, 0, ',', '.') }}</td>
                        <td>{{ ucfirst($transaction->payment_status) }}</td>
                        <td>
                            @if ($transaction->payment_status == 'dp')
                                Rp {{ number_format($transaction->dp_amount, 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if ($transaction->payment_status == 'dp')
                                Rp {{ number_format($transaction->total - $transaction->dp_amount, 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            <div class="product-list">
                                @foreach ($transaction->details as $detail)
                                    <div class="product-item">
                                        <img src="data:image/png;base64,{{ $detail->product->image }}"
                                            alt="{{ $detail->product->name }}">
                                        <div class="product-info">
                                            <span class="product-name">{{ $detail->product->name }}</span>
                                            <span class="product-qty">({{ $detail->quantity }}x)</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </td>
                        <td>{{ $transaction->note ?? '-' }}</td>
                        <td>
                            <div class="action-buttons">
                                {{-- Tombol Edit (Hanya bisa diakses oleh kasir dan admin) --}}
                                @if (
                                    (Auth::user()->role == 'kasir' || Auth::user()->role == 'admin') && 
                                        ($transaction->payment_status == 'dp'))
                                    <a href="{{ route('transactions.edit', $transaction->id) }}" class="btn btn-info">
                                        <i class="fa-solid fa-pen"></i> Edit
                                    </a>
                                @endif

                                {{-- Tombol Lunas (Hanya bisa diakses oleh admin dan kasir jika belum lunas) --}}
                                @if (
                                        (Auth::user()->role == 'kasir' || Auth::user()->role == 'admin') &&
                                        ($transaction->payment_status == 'dp' || $transaction->payment_status == 'bayar nanti')
                                    )
                                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalPayment-{{ $transaction->id }}">
                                        <i class="fa-solid fa-money-bill"></i> Lunas
                                    </button>
                                @endif

                                <a href="{{ route('transactions.print', $transaction->id) }}" class="btn btn-sm btn-success" target="_blank">
                                    Cetak PDF
                                </a>

                                {{-- Tombol Kirim ke WA --}}
                                <form id="waDownloadForm-{{ $transaction->id }}" action="{{ route('transactions.downloadPdfAndOpenWa', $transaction->id) }}" method="POST">
                                    @csrf
                                    <button type="button" class="btn btn-success" onclick="openWaThenDownload('{{ '62' . ltrim($transaction->phone_number, '0') }}', {{ $transaction->id }})">
                                        <i class="fa-brands fa-whatsapp"></i>
                                    </button>
                                </form>

                                {{-- Tombol Hapus (Hanya bisa diakses oleh admin) --}}
                                @if (Auth::user()->role == 'admin')
                                    <form action="{{ route('transactions.destroy', $transaction->id) }}" method="POST"
                                        class="delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-delete"
                                            onclick="confirmDelete(this)">
                                            <i class="fa-solid fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>

                    </tr>

                    <!-- Modal -->
                    <div class="modal fade" id="modalPayment-{{ $transaction->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <form action="{{ route('transactions.updatePaymentStatus', $transaction->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Input Nominal Pembayaran</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="" id="modal-total-{{ $transaction->id }}" value="{{ ($transaction->total - $transaction->dp_amount) }}">

                                        <label for="info-payment">Sisa Pembayaran : <span>{{ ($transaction->total - $transaction->dp_amount) }}</span></label><br>
                                        <div style="border-bottom: 1px dashed rgb(149, 149, 149)"></div>
                                        <br>

                                        <label for="payment">Nominal Bayar</label>
                                        <input type="number" class="form-control" name="payment" id="payment-input-{{ $transaction->id }}" min="0" step="1000" required>

                                        <div class="mt-3">
                                            <strong>Kembalian: </strong>
                                            <span id="change-output-{{ $transaction->id }}">Rp 0</span>
                                        </div>

                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-success">Lunasi</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
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

        function openWaThenDownload(phone, id) {
            // Buka WA dulu
            window.open('https://wa.me/' + phone, '_blank');

            // Submit form setelah delay (biar ga ke-block browser)
            setTimeout(() => {
                document.getElementById('waDownloadForm-' + id).submit();
            }, 1000); // kasih jeda 1 detik biar user lihat WA terbuka

            // Swal tetap tampil
            Swal.fire({
                icon: 'success',
                title: 'Struk sedang dikirim!',
                text: 'Struk transaksi akan segera diunduh.',
                timer: 5000,
                showConfirmButton: false
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            @foreach ($transactions as $transaction)
                const paymentInput{{ $transaction->id }} = document.getElementById('payment-input-{{ $transaction->id }}');
                const changeOutput{{ $transaction->id }} = document.getElementById('change-output-{{ $transaction->id }}');
                const totalValue{{ $transaction->id }} = parseInt(document.getElementById('modal-total-{{ $transaction->id }}').value) || 0;

                function formatRupiah(value) {
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0
                    }).format(value);
                }

                function updateChange{{ $transaction->id }}() {
                    const payment = parseInt(paymentInput{{ $transaction->id }}.value) || 0;
                    const change = payment - totalValue{{ $transaction->id }};
                    changeOutput{{ $transaction->id }}.textContent = formatRupiah(change >= 0 ? change : 0);
                }

                paymentInput{{ $transaction->id }}.addEventListener('input', updateChange{{ $transaction->id }});
            @endforeach
        });
    </script>
@endsection
