@extends('layouts.app')

@section('title', 'Rekomendasi Produk')

@section('content')
    <div class="container mt-4">
        <h2>Tambah Transaksi</h2>
        <form action="{{ route('transactions.store') }}" method="POST">
            @csrf

            {{-- Input Budget --}}
            <div class="mb-3">
                <label for="budget" class="form-label">Budget</label>
                <div class="input mb-6 row">
                    <div class="col">
                        <input type="number" class="form-control budget-input" id="budget-min" name="budget_min" placeholder="Min">
                    </div>
                    <div class="col-auto d-flex align-items-center">
                        <p class="mb-0">-</p>
                    </div>
                    <div class="col">
                        <input type="number" class="form-control budget-input" id="budget-max" name="budget_max" placeholder="Max">
                    </div>
                </div>
            </div>

            {{-- Pilih Kategori --}}
            <div class="mb-3">
                <label for="category" class="form-label">Pilih Kategori</label>
                <select class="form-control category-select" id="category" name="category">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Pilih Produk --}}
            <div class="mb-3">
                <label for="product" class="form-label mt-2">Pilih Produk</label>
                <select name="products[0][id]" class="form-control product-select" id="product">
                    <option value="">-- Pilih Produk --</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }} - Rp{{ number_format($product->price, 2) }}</option>
                    @endforeach
                </select>
            </div>

            <a href="{{ route('transactions.create', ['category' => $product->category_id, 'product' => $product->id]) }}" class="btn btn-primary">
    Tambah Transaksi
</a>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let categorySelect = document.getElementById('category');
            let budgetMinInput = document.getElementById('budget-min');
            let budgetMaxInput = document.getElementById('budget-max');
            let productSelect = document.getElementById('product');

            function updateProducts() {
                let category = categorySelect.value;
                let budgetMin = budgetMinInput.value;
                let budgetMax = budgetMaxInput.value;

                fetch(`{{ route('products.filter') }}?category=${category}&budget_min=${budgetMin}&budget_max=${budgetMax}`)
                    .then(response => response.json())
                    .then(data => {
                        productSelect.innerHTML = '<option value="">-- Pilih Produk --</option>';
                        data.forEach(product => {
                            let option = document.createElement('option');
                            option.value = product.id;
                            option.textContent = `${product.name} - Rp${parseFloat(product.price).toLocaleString()}`;
                            productSelect.appendChild(option);
                        });
                    });
            }

            categorySelect.addEventListener('change', updateProducts);
            budgetMinInput.addEventListener('input', updateProducts);
            budgetMaxInput.addEventListener('input', updateProducts);
        });

        document.getElementById("redirect-btn").addEventListener("click", function() {
            let category = document.querySelector(".category-select").value;
            let product = document.querySelector(".product-select").value;

            if (!category || !product) {
                alert("Silakan pilih kategori dan produk terlebih dahulu!");
                return;
            }

            // Gunakan Blade Syntax dengan format string
            let transactionUrl = "{{ route('transactions.create') }}";
            window.location.href = transactionUrl + `?category=${category}&product=${product}`;
        });
    </script>

@endsection
