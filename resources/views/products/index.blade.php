@extends('layouts.app')

@section('title', 'Produk')

@section('content')
    <div class="container mt-4">
        <h2 class="mb-3 text-dark">Daftar Produk</h2>

        <a href="{{ route('products.create') }}" class="btn btn-custom mb-3">
            <i class="fa-solid fa-plus"></i> Tambah Produk
        </a>

        <div class="table-responsive">
            <table class="table table-bordered table-hover text-center align-middle">
                <thead class="table-header">
                    <tr>
                        <th>No</th>
                        <th>Produk</th>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                @if ($product->image)
                                    <img src="data:image/png;base64,{{ $product->image }}" width="100">
                                @else
                                    <span class="text-muted">Tidak ada gambar</span>
                                @endif
                            </td>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->category->name }}</td>
                            <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                            <td>{{ $product->stock }}</td>
                            <td>
                                <a href="{{ route('products.edit', $product->id) }}" class="btn btn-edit">
                                    <i class="fa-solid fa-pen"></i> Edit
                                </a>
                                <form action="{{ route('products.destroy', $product->id) }}" method="POST"
                                    class="d-inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-delete" onclick="confirmDelete(this)">
                                        <i class="fa-solid fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(button) {
            Swal.fire({
                title: "Apakah Anda yakin?",
                text: "Produk yang dihapus tidak bisa dikembalikan!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Ya, Hapus!",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    button.closest('form').submit();
                }
            });
        }
    </script>

    <style>
        /* Warna utama */
        :root {
            --main-color: #FCC6FF;
            --dark-color: #222222;
            --danger-color: #ff6b6b;
        }

        /* Tombol Custom */
        .btn-custom {
            background-color: var(--main-color);
            border: none;
            color: #222;
            font-weight: bold;
            padding: 10px 15px;
            border-radius: 5px;
            transition: 0.3s;
        }

        .btn-custom:hover {
            background-color: #e6b3e6;
        }

        /* Header Tabel */
        .table-header {
            background-color: var(--main-color);
            color: black;
        }

        /* Hover pada Baris Tabel */
        .table tbody tr:hover {
            background-color: #f5e6f7;
        }

        /* Tombol Edit & Hapus */
        .btn-edit {
            background-color: var(--main-color);
            border: none;
            color: black;
            padding: 8px 12px;
            border-radius: 5px;
            font-size: 14px;
            transition: 0.3s;
        }

        .btn-edit:hover {
            background-color: #e6b3e6;
        }

        .btn-delete {
            background-color: var(--danger-color);
            border: none;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            font-size: 14px;
            transition: 0.3s;
        }

        .btn-delete:hover {
            background-color: #ff4d4d;
        }
    </style>
@endsection
