@extends('layouts.app')

@section('title', 'Edit Produk')

@section('content')
    <div class="container mt-4">
        <div class="edit-produk-container">
            <h2 class="text-center mb-4">Edit Produk</h2>
            <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label">Nama Produk</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ $product->name }}"
                        required>
                </div>

                <div class="mb-3">
                    <label for="category_id" class="form-label">Kategori</label>
                    <select class="form-select" id="category_id" name="category_id" required>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="price" class="form-label">Harga</label>
                    <input type="number" class="form-control" id="price" name="price" value="{{ $product->price }}"
                        required>
                </div>

                <div class="mb-3">
                    <label for="stock" class="form-label">Stock</label>
                    <input type="number" class="form-control" id="stock" name="stock" value="{{ $product->stock }}" required>
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label">Gambar Produk</label>
                    <div class="mb-2">
                        @if ($product->image)
                            <img id="currentImage" src="{{ asset('images/' . $product->image) }}" width="120"
                                height="120" class="rounded shadow-sm">
                        @endif
                    </div>
                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                    <div class="mt-2">
                        <img id="previewImage" src="" width="120" height="120"
                            class="rounded shadow-sm d-none">
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-update">Update</button>
                </div>
            </form>
        </div>
    </div>

    <style>
        /* Container Styling */
        .edit-produk-container {
            max-width: 600px;
            background: white;
            border-radius: 10px;
            padding: 25px;
            margin: auto;
            box-shadow: 2px 4px 10px rgba(0, 0, 0, 0.1);
        }

        .edit-produk-container h2 {
            color: #222;
            font-weight: bold;
        }

        /* Button */
        .btn-update {
            background-color: #FCC6FF;
            border: none;
            color: #222;
            font-weight: bold;
            padding: 10px 15px;
            border-radius: 5px;
            transition: 0.3s;
        }

        .btn-update:hover {
            background-color: #e6b3e6;
        }
    </style>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const categorySelect = document.getElementById("category_id");
            const priceInput = document.getElementById("price");
            const form = document.querySelector("form");
            const imageInput = document.getElementById("image");
            const previewImage = document.getElementById("previewImage");
            const currentImage = document.getElementById("currentImage");

            // Animasi saat kategori diganti
            categorySelect.addEventListener("change", function() {
                this.style.backgroundColor = "#FCC6FF";
                setTimeout(() => {
                    this.style.backgroundColor = "white";
                }, 300);
            });

            // Validasi harga agar tidak negatif atau kosong
            form.addEventListener("submit", function(event) {
                if (priceInput.value === "" || priceInput.value < 0) {
                    alert("Harga tidak boleh kosong atau negatif!");
                    event.preventDefault();
                }
            });

            // Preview gambar sebelum diunggah
            imageInput.addEventListener("change", function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImage.src = e.target.result;
                        previewImage.classList.remove("d-none");
                        if (currentImage) {
                            currentImage.style.display = "none"; // Sembunyikan gambar lama
                        }
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>
@endsection
