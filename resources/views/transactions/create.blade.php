@extends('layouts.app')

@section('title', 'Tambah Transaksi')

@section('content')
    <div class="container">
        <h2>Tambah Transaksi</h2>
        <form id="transaction-form" method="POST">
            @csrf
            <div class="info-customers d-flex flex-row justify-content-between">
                <div class="mb-3 col-5">
                    <label for="customer_name" class="form-label">Nama Customer</label>
                    <input type="text" class="form-control" id="customer_name" name="customer_name" required>
                    <ul id="customer-list" class="list-group" style="display:none; position: absolute; z-index: 10;"></ul>
                </div>

                <div class="mb-3 col-5 position-relative">
                    <label for="phone-number" class="form-label">No Hp</label>
                    <input type="text" class="form-control" id="phone_number" name="phone_number" required>
                    <ul id="phone-list" class="list-group" style="display:none; position: absolute; z-index: 10;"></ul>
                </div>
            </div>

            <div class="info-customers d-flex flex-row justify-content-between">
                <div class="mb-3 col-5">
                    <label for="customer_address" class="form-label">Alamat</label>
                    <input type="text" class="form-control" id="customer_address" name="customer_address" required>
                </div>

                <div class="mb-3 col-5">
                    <label for="order_date" class="form-label">Tanggal Pesanan</label>
                    <input type="date" class="form-control" id="order_date" name="order_date" value="{{ date('Y-m-d') }}"
                        required>
                </div>
            </div>

            <div class="info-customers d-flex flex-row justify-content-between mb-3">
                <div class="mb-3 col-5">
                    <label for="pickup_date" class="form-label">Tanggal Pengambilan</label>
                    <input type="date" class="form-control" id="pickup_date" name="pickup_date" value="{{ date('Y-m-d') }}"
                        required>
                </div>

                <div class="mb-3 col-5">
                    <label for="bugdet_max" class="form-label">Budget Rekomendasi</label>
                    <input type="text" class="form-control" id="budget_max" name="budget_max" value=""
                        required>
                </div>
            </div>

            <div id="products-container">
                <div class="product-row border p-3 mb-3">
                    <label for="category" class="form-label">Pilih Kategori</label>
                    <select class="form-control category-select" id="category-select">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" 
                                {{ $category->id == old('category', $selectedCategory ?? '') ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>

                    <label for="product" class="form-label mt-2">Pilih Produk</label>
                    <select name="products[0][id]" class="form-control product-select" id="product-select">
                        <option value="">-- Pilih Produk --</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" 
                                data-price="{{ $product->price }}"
                                data-stock="{{ $product->stock }}"
                                {{ $product->id == old('product', $selectedProduct ?? '') ? 'selected' : '' }}>
                                {{ $product->name }} - Rp{{ number_format($product->price, 2) }}
                            </option>
                        @endforeach
                    </select>
                    <label class="stock-label mt-2">Stok: <span class="stock-value">
                        {{ $selectedStock ?? 0 }}
                    </span></label>
                    <input type="number" name="products[0][quantity]" class="form-control quantity-input mt-2"
                        placeholder="Jumlah" min="1" required>
                    <label class="total-label mt-2">Subtotal: Rp <span class="subtotal-value">0</span></label>
                    <br>
                    <button type="button" class="btn btn-danger btn-sm remove-product mt-2">Hapus</button>
                </div>
            </div>
            
            <button type="button" id="add-product" class="btn btn-secondary">Tambah Produk</button>

            <div class="mb-3">
                <label for="note" class="form-label">Catatan Tambahan</label>
                <textarea class="form-control" id="note" name="note" rows="3"></textarea>
            </div>

            <div class="info-customers d-flex flex-row justify-content-between">
                <div class="form-group col-5">
                    <label for="payment_status">Jenis Pembayaran</label>
                    <select id="payment_status" name="payment_status" class="form-control" required onchange="toggleDpInput()">
                        <option value="lunas">Lunas</option>
                        <option value="dp">DP</option>
                    </select>
                </div>

                <div class="form-group col-5">
                    <label for="payment_method">Metode Pembayaran</label>
                    <select id="payment_method" name="payment_method" class="form-control" required onchange="">
                        <option value="cash">Cash</option>
                        <option value="transfer">Transfer</option>
                    </select>
                </div>
            </div>

            <div class="form-group" id="dp_input_container" style="display: none;">
                <label for="dp_amount">Nominal DP</label>
                <input type="number" id="dp_amount" name="dp_amount" class="form-control" min="0" step="1000"
                    placeholder="Masukkan nominal DP">
            </div>

            <div class="mt-3">
                <h5>Total Harga: Rp <span id="total-price">0</span></h5>
            </div>
            <button type="button" class="btn btn-primary submit-btn" data-action="{{ route('transactions.store') }}">Checkout</button>
            <button type="button" class="btn btn-secondary submit-btn" data-action="{{ route('cart.store') }}">Keranjang</button>
        </form>
    </div>

    <script>
        let products = @json($products);
        console.log("DATA PRODUCTS:", products);
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
                    let selectedCategory = event.target.value ? parseInt(event.target.value) : null;
                    let productSelect = row.querySelector('.product-select');

                    // Reset dropdown produk
                    productSelect.innerHTML = `<option value="">-- Pilih Produk --</option>`;

                    // Ambil nilai budgetMax yang sudah diisi user
                    let budgetMax = parseInt(document.getElementById('budget_max').value) || Infinity;
                    let priceLimit;
                    // Filter produk berdasarkan kategori dan budget
                    if (isNaN(budgetMax) || budgetMax <= 0) {
                        budgetMax = Infinity; // Jika tidak valid (NaN atau <= 0), anggap tidak ada batasan harga
                        priceLimit = Infinity; // Set priceLimit to Infinity jika tidak ada batasan harga
                    } else {
                        priceLimit = 10000; // Jika ada budgetMax valid, gunakan priceLimit 10000
                    }
                    console.log("Kategori yang dipilih:", selectedCategory);
                    console.log("Budget max:", budgetMax);
                    console.log("Semua produk:", products);
                    products.forEach(p => {
                        console.log(`Product ID: ${p.id}, Category ID: ${p.category_id}, Price: ${p.price}`);
                    });

                    let filteredProducts = products
                        .map(product => ({
                            ...product,
                            price: parseFloat(product.price),
                            category_id: parseInt(product.category_id)
                        }))
                        .filter(product => {
                            let matchCategory = selectedCategory ? product.category_id === selectedCategory : true;
                            let withinBudget = (budgetMax === Infinity || Math.abs(product.price - budgetMax) <= priceLimit);
                            console.log(`Check product ${product.name} → Category match: ${matchCategory}, Budget match: ${withinBudget}`);

                            return matchCategory && withinBudget;
                        });

                    // let filteredProducts = products
                    //     .filter(product =>
                    //         product.category_id == selectedCategory &&
                    //         Math.abs(parseFloat(product.price) - budgetMax) <= priceLimit
                    //     )
                    //     .sort((a, b) =>
                    //         Math.abs(parseFloat(a.price) - budgetMax) - Math.abs(parseFloat(b.price) - budgetMax)
                    //     );

                    console.log("Hasil filter:", filteredProducts);

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

        document.addEventListener("DOMContentLoaded", function () {
            // Tangani format DP input


            // Tangani form submit sesuai button yang diklik
            document.querySelectorAll('.submit-btn').forEach(button => {
                button.addEventListener('click', function () {
                    let form = document.getElementById('transaction-form');
                    form.action = this.getAttribute('data-action'); // Set action sesuai button
                    this.disabled = true; // Hindari multiple submit
                    form.submit();
                });
            });

            // Hapus format ribuan sebelum form dikirim
            document.getElementById('transaction-form').addEventListener('submit', function () {
                dpInput.value = dpInput.value.replace(/\,/g, ""); // Hapus titik sebelum dikirim ke server
            });
        });

        document.getElementById('budget_max').addEventListener('input', function () {
            let budgetMax = this.value;

            fetch("{{ route('transactions.create') }}?budget_max=" + budgetMax)
                .then(response => response.text())
                .then(html => {
                    let parser = new DOMParser();
                    let doc = parser.parseFromString(html, 'text/html');
                    document.getElementById('products-container').innerHTML = doc.getElementById('products-container').innerHTML;
                });
        });

        // Mendapatkan elemen input nama customer
        const customerNameInput = document.getElementById('customer_name');
        const customerList = document.getElementById('customer-list');
        const phoneNumberInput = document.getElementById('phone_number');
        const customerAddressInput = document.getElementById('customer_address');
        const phoneList = document.getElementById('phone-list');

        // Fungsi untuk mencari customer berdasarkan nama yang dimasukkan
        customerNameInput.addEventListener('input', function() {
            const query = customerNameInput.value;
            
            if (query.length > 2) {  // Mulai mencari setelah 3 karakter
                fetch(`/transactions/customers/search?name=${query}`)
                    .then(response => response.json())
                    .then(data => {
                        // Menampilkan hasil pencarian dalam daftar
                        customerList.innerHTML = '';
                        data.forEach(customer => {
                            const listItem = document.createElement('li');
                            listItem.textContent = customer.name;
                            listItem.classList.add('list-group-item');
                            listItem.addEventListener('click', function() {
                                // Isi input dengan nama yang dipilih
                                customerNameInput.value = customer.name;
                                phoneNumberInput.value = customer.phone_number;
                                customerAddressInput.value = customer.address;
                                customerList.style.display = 'none';  // Sembunyikan daftar setelah memilih
                            });
                            customerList.appendChild(listItem);
                        });
                        customerList.style.display = 'block';  // Tampilkan daftar
                    });
            } else {
                customerList.style.display = 'none';  // Sembunyikan daftar jika input kurang dari 3 karakter
            }
        });

        // Menyembunyikan daftar customer jika user mengklik di luar input
        document.addEventListener('click', function(e) {
            if (!customerNameInput.contains(e.target) && !customerList.contains(e.target)) {
                customerList.style.display = 'none';
            }
        });

        // Autocomplete untuk nomor HP
        phoneNumberInput.addEventListener('input', function() {
            const query = phoneNumberInput.value;

            if (query.length > 2) {
                fetch(`/transactions/customers/search?name=${query}`) // masih pakai route yang sama
                    .then(response => response.json())
                    .then(data => {
                        phoneList.innerHTML = '';
                        data.forEach(customer => {
                            const listItem = document.createElement('li');
                            listItem.textContent = customer.phone_number;
                            listItem.classList.add('list-group-item');
                            listItem.addEventListener('click', function() {
                                phoneNumberInput.value = customer.phone_number;
                                customerNameInput.value = customer.name;
                                customerAddressInput.value = customer.address;
                                phoneList.style.display = 'none';
                            });
                            phoneList.appendChild(listItem);
                        });
                        phoneList.style.display = 'block';
                    });
            } else {
                phoneList.style.display = 'none';
            }
        });

        // Sembunyikan daftar saat klik di luar
        document.addEventListener('click', function(e) {
            if (!phoneNumberInput.contains(e.target) && !phoneList.contains(e.target)) {
                phoneList.style.display = 'none';
            }
        });
    </script>
    <style>
        .container {
            background-color: #FCC6FF;
            padding: 20px;
            border-radius: 10px;
        }

        h2,
        h5 {
            color: #6A0572;
            /* Warna ungu yang lebih gelap untuk kontras */
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

        .btn-secondary {
            background-color: #A73489;
            border-color: #A73489;
        }

        .btn-secondary:hover {
            background-color: #870E69;
            border-color: #870E69;
        }

        .btn-danger {
            background-color: #FF4081;
            border-color: #FF4081;
        }

        .btn-danger:hover {
            background-color: #D81B60;
            border-color: #D81B60;
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

        .total-label,
        .stock-label {
            color: #6A0572;
            font-weight: bold;
        }

        .list-group:hover {
            cursor: pointer;
            background-color: turquoise;
        }
    </style>

@endsection
