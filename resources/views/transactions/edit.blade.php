@extends('layouts.app')

@section('title', 'Edit Transaksi')

@section('content')
    <div class="container">
        <h2>Edit Transaksi</h2>
        <form action="{{ route('transactions.update', $transaction->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="info-customers d-flex flex-row justify-content-between">
                <div class="mb-3 col-5">
                    <label for="customer_name" class="form-label">Nama Customer</label>
                    <input type="text" class="form-control" id="customer_name" name="customer_name"
                        value="{{ $transaction->customer->name ?? "-" }}" required readonly>
                </div>

                <div class="mb-3 col-5">
                    <label for="phone-number" class="form-label">No Hp</label>
                    <input type="text" class="form-control" id="phone_number" name="phone_number"
                        value="{{ $transaction->phone_number }}" required>
                </div>
            </div>

            <div class="info-customers d-flex flex-row justify-content-between">
                <div class="mb-3 col-5">
                    <label for="customer_address" class="form-label">Alamat</label>
                    <input type="text" class="form-control" id="customer_address" name="customer_address"
                        value="{{ $transaction->address }}" required>
                </div>

                <div class="mb-3 col-5">
                    <label for="order_date" class="form-label">Tanggal Pesanan</label>
                    <input type="date" class="form-control" id="order_date" name="order_date" value="{{ $transaction->order_date}}"
                        required>
                </div>
            </div>

            <div class="info-customers d-flex flex-row justify-content-between mb-3">
                <div class="mb-3 col-5">
                    <label for="pickup_date" class="form-label">Tanggal Pengambilan</label>
                    <input type="date" class="form-control" id="pickup_date" name="pickup_date" value="{{ $transaction->pickup_date }}"
                        required>
                </div>
            </div>

            <div id="products-container">
                @foreach ($transaction->details as $index => $detail)
                    <div class="product-row border p-3 mb-3">
                        <label for="category" class="form-label">Pilih Kategori</label>
                        <select class="form-control category-select">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ $category->id == $detail->product->category_id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>

                        <label for="product" class="form-label mt-2">Pilih Produk</label>
                        <select name="products[{{ $index }}][id]" class="form-control product-select">
                            <option value="">-- Pilih Produk --</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" data-price="{{ $product->price }}"
                                    data-stock="{{ $product->stock }}" data-category="{{ $product->category_id }}"
                                    {{ $product->id == $detail->product_id ? 'selected' : '' }}>
                                    {{ $product->name }} - Rp {{ number_format($product->price, 0, ',', '.') }}
                                </option>
                            @endforeach
                        </select>

                        <label class="stock-label mt-2">Stok: <span
                                class="stock-value">{{ $detail->product->stock }}</span></label>

                        <input type="number" name="products[{{ $index }}][quantity]"
                            class="form-control quantity-input mt-2" placeholder="Jumlah" min="1"
                            value="{{ $detail->quantity }}" required>
                            
                        <label class="total-label mt-2">Subtotal: Rp <span
                                class="subtotal-value">{{ number_format($detail->subtotal, 0, ',', '.') }}</span></label>
                        <br>
                        <button type="button" class="btn btn-danger btn-sm remove-product mt-2">Hapus</button>
                    </div>
                @endforeach
            </div>
            <button type="button" id="add-product" class="btn btn-secondary">Tambah Produk</button>

            <div class="mb-3">
                <label for="note" class="form-label">Catatan Tambahan</label>
                <textarea class="form-control" id="note" name="note" rows="3">{{ $transaction->note }}</textarea>
            </div>

            <div class="info-customers d-flex flex-row justify-content-between">
                <div class="form-group col-5">
                    <label for="payment_status">Jenis Pembayaran</label>
                    <select id="payment_status" name="payment_status" class="form-control" required onchange="toggleDpInput()">
                        <option value="lunas" {{ $transaction->payment_status == 'lunas' ? 'selected' : '' }}>Lunas</option>
                        <option value="dp" {{ $transaction->payment_status == 'dp' ? 'selected' : '' }} >DP</option>
                        <option value="bayar nanti" {{ $transaction->payment_status == 'bayar nanti' ? 'selected' : '' }}>Bayar Nanti</option>
                    </select>
                </div>

                <div class="form-group col-5">
                    <label for="payment_method">Metode Pembayaran</label>
                    <select id="payment_method" name="payment_method" class="form-control" required onchange="">
                        <option value="cash" {{ $transaction->payment_method == 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="transfer" {{ $transaction->payment_method == 'transfer' ? 'selected' : '' }}>Transfer</option>
                    </select>
                </div>
            </div>

            <div class="form-group" id="dp_input_container"
                style="{{ $transaction->payment_status == 'dp' ? 'display: block;' : 'display: none;' }}">
                <label for="dp_amount">Nominal DP</label>
                <input type="number" id="dp_amount" name="dp_amount" class="form-control" min="0" step="1000"
                    placeholder="Masukkan nominal DP" value="{{ $transaction->dp_amount }}">
            </div>

            <div class="mt-3">
                <h5>Total Harga: Rp <span
                        id="total-price">{{ number_format($transaction->total, 0, ',', '.') }}</span></h5>
            </div>
            
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </form>
    </div>

    <script>
        let products = @json($products);
        document.addEventListener("DOMContentLoaded", function() {
            let container = document.getElementById('products-container');
            let addButton = document.getElementById('add-product');
            let totalPrice = document.getElementById('total-price');

            function updateTotal() {
                let total = 0;
                document.querySelectorAll('.product-row').forEach(row => {
                    let quantity = parseInt(row.querySelector('.quantity-input').value) || 0;
                    let price = parseInt(row.querySelector('.product-select option:checked')?.dataset
                        .price) || 0;
                    let subtotal = quantity * price;
                    row.querySelector('.subtotal-value').textContent = new Intl.NumberFormat('id-ID')
                        .format(subtotal);
                    total += subtotal;
                });
                totalPrice.textContent = new Intl.NumberFormat('id-ID').format(total);
            }

            // Event Listener untuk memilih kategori dan filter produk
            container.addEventListener('change', function(event) {
                let row = event.target.closest('.product-row');
                if (!row) return;

                if (event.target.classList.contains('category-select')) {
                    let selectedCategory = event.target.value;
                    let productSelect = row.querySelector('.product-select');

                    // Reset dropdown produk
                    productSelect.innerHTML = `<option value="">-- Pilih Produk --</option>`;

                    // Filter produk berdasarkan kategori yang dipilih
                    let filteredProducts = products.filter(product => product.category_id ==
                        selectedCategory);

                    filteredProducts.forEach(product => {
                        let option = document.createElement('option');
                        option.value = product.id;
                        option.dataset.price = product.price;
                        option.dataset.stock = product.stock;
                        option.textContent =
                            `${product.name} - Rp ${new Intl.NumberFormat('id-ID').format(product.price)}`;
                        productSelect.appendChild(option);
                    });

                    row.querySelector('.stock-value').textContent = "0";
                    row.querySelector('.subtotal-value').textContent = "0";
                }

                if (event.target.classList.contains('product-select')) {
                    let selectedOption = event.target.options[event.target.selectedIndex];
                    let price = parseInt(selectedOption.dataset.price) || 0;
                    let stock = parseInt(selectedOption.dataset.stock) || 0;
                    row.querySelector('.stock-value').textContent = stock;

                    let quantityInput = row.querySelector('.quantity-input');
                    quantityInput.addEventListener('input', function() {
                        let quantity = parseInt(quantityInput.value) || 0;
                        row.querySelector('.subtotal-value').textContent = (quantity > stock) ?
                            "(Melebihi stock)" : new Intl.NumberFormat('id-ID').format(quantity *
                                price);
                        updateTotal();
                    });
                }
            });

            addButton.addEventListener('click', function() {
                let index = container.children.length;
                let newRow = container.children[0].cloneNode(true);
                newRow.innerHTML = newRow.innerHTML.replace(/\[0\]/g, `[${index}]`);
                newRow.querySelector('.stock-value').textContent = "0";
                newRow.querySelector('.subtotal-value').textContent = "0";
                newRow.querySelector('.quantity-input').value = "";
                newRow.querySelector('.product-select').innerHTML =
                    `<option value="">-- Pilih Produk --</option>`;
                container.appendChild(newRow);
                updateTotal();
            });

            container.addEventListener('click', function(event) {
                if (event.target.classList.contains('remove-product')) {
                    if (container.children.length > 1) {
                        event.target.closest('.product-row').remove();
                        updateTotal();
                    }
                }
            });
        });


        function toggleDpInput() {
            let paymentStatus = document.getElementById("payment_status").value;
            let dpInputContainer = document.getElementById("dp_input_container");

            if (paymentStatus === "dp") {
                dpInputContainer.style.display = "block";
                document.getElementById("dp_amount").setAttribute("required", "true");
            } else {
                dpInputContainer.style.display = "none";
                document.getElementById("dp_amount").removeAttribute("required");
            }
        }
    </script>

    <style>
        .container {
            background-color: #FCC6FF;
            padding: 20px;
            border-radius: 10px;
        }

        .table {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
        }

        .product-row {
            background-color: white;
            border-radius: 8px;
            padding: 10px;
        }

        h2,
        h5 {
            color: #6A0572;
        }

        .form-control {
            border-color: #6A0572;
        }

        .btn-primary {
            background-color: #6A0572;
            border-color: #6A0572;
        }

        .btn-primary:hover {
            background-color: #4A034C;
            border-color: #4A034C;
        }
    </style>

@endsection
