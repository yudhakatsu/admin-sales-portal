@extends('layouts.app')

@section('title', 'Tambah Produk')

@section('content')
    <div class="container mt-4">
        <div class="tambah-produk-container">
            <h2 class="text-center mb-4">Tambah Produk</h2>
            <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Nama Produk</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>

                <div class="mb-3">
                    <label for="category_id" class="form-label">Kategori</label>
                    <select class="form-select" id="category_id" name="category_id" required>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="price" class="form-label">Harga</label>
                    <input type="number" class="form-control" id="price" name="price" required>
                </div>

                <div class="mb-3">
                    <label for="stock" class="form-label">Stock</label>
                    <input type="number" class="form-control" id="stock" name="stock" required>
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label">Upload Gambar</label>
                    <input type="file" class="form-control" id="image" name="image" required>
                    <div class="mt-2">
                        <img id="previewImage" src="" width="120" height="120" class="rounded shadow-sm d-none">
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <style>
        /* Container Styling */
        .tambah-produk-container {
            max-width: 600px;
            background: white;
            border-radius: 10px;
            padding: 25px;
            margin: auto;
            box-shadow: 2px 4px 10px rgba(0, 0, 0, 0.1);
        }

        .tambah-produk-container h2 {
            color: #222;
            font-weight: bold;
        }

        /* Button */
        .btn-submit {
            background-color: #FCC6FF;
            border: none;
            color: #222;
            font-weight: bold;
            padding: 10px 15px;
            border-radius: 5px;
            transition: 0.3s;
        }

        .btn-submit:hover {
            background-color: #e6b3e6;
        }
    </style>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const inputs = document.querySelectorAll("input, select");
            const form = document.querySelector("form");
            const imageInput = document.getElementById("image");
            const previewImage = document.getElementById("previewImage");

            // Animasi saat input difokuskan
            inputs.forEach(input => {
                input.addEventListener("focus", function() {
                    this.style.borderColor = "#FCC6FF";
                    this.style.boxShadow = "0px 0px 8px rgba(252, 198, 255, 0.7)";
                });

                input.addEventListener("blur", function() {
                    this.style.borderColor = "#ccc";
                    this.style.boxShadow = "none";
                });
            });

            // Preview gambar sebelum diunggah
            imageInput.addEventListener("change", function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImage.src = e.target.result;
                        previewImage.classList.remove("d-none");
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Konfirmasi sebelum submit
            form.addEventListener("submit", function(event) {
                if (!confirm("Apakah Anda yakin ingin menambahkan produk ini?")) {
                    event.preventDefault();
                }
            });
        });
    </script>
@endsection
